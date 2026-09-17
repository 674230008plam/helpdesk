<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\EventDispatcher;
use App\Core\FileUploader;
use App\Enums\TicketStatus;
use App\Notifications\EmailNotificationService;
use App\Observers\TicketObserver;
use App\Repositories\TicketRepository;
use App\Services\TicketService;
use App\Services\TicketStatusService;

class TicketController
{
    private TicketRepository $repo;
    private TicketStatusService $statusService;
    private TicketService $ticketService;
    private EventDispatcher $events;

    public function __construct()
    {
        $this->repo = new TicketRepository();
        $this->events = new EventDispatcher();
        $this->statusService = new TicketStatusService($this->repo, $this->events);
        $this->ticketService = new TicketService($this->repo, $this->events);

        $observer = new TicketObserver(new EmailNotificationService());
        $this->events->listen('ticket.status_changed', function ($p) use ($observer) {
            $email = $p['ticket']['user_email'] ?? 'admin@helpdesk.local';
            $observer->handleStatusChanged($p['ticket'], $p['from'], $p['to'], $email);
        });
        $this->events->listen('ticket.created', function ($t) use ($observer) {
            $observer->handleCreated($t, 'admin@helpdesk.local');
        });
    }

    public function index(): void
    {
        $user = Auth::user();
        $tickets = match ($user['role']) {
            'admin' => $this->repo->all(),
            'technician' => $this->repo->findByTechnician((int)$user['id']),
            default => $this->repo->findByUser((int)$user['id']),
        };
        require_once dirname(__DIR__, 2) . '/views/tickets/index.php';
    }

    public function show(string $id): void
    {
        $ticket = $this->repo->find((int)$id);
        if (!$ticket) {
            http_response_code(404);
            die("404 Not Found: ไม่พบใบแจ้งซ่อม");
        }
        $comments = $this->repo->getComments((int)$id);
        $logs = $this->repo->getLogs((int)$id);
        $rating = $this->repo->getRating((int)$id);
        $technicians = $this->repo->getTechnicians();

        require_once dirname(__DIR__, 2) . '/views/tickets/show.php';
    }

    public function create(): void
    {
        $categories = $this->repo->getCategories();
        require_once dirname(__DIR__, 2) . '/views/tickets/create.php';
    }

    public function store(): void
    {
        $payload = [
            'user_id' => Auth::id(),
            'category_id' => (int)($_POST['category_id'] ?? 1),
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'priority' => $_POST['priority'] ?? 'Medium',
        ];

        $file = $_FILES['image'] ?? null;
        $ticket = $this->ticketService->createTicket($payload, $file);

        header("Location: /smart-it-helpdesk/public/tickets/{$ticket['id']}");
        exit;
    }

    public function updateStatus(string $id): void
    {
        $ticket = $this->repo->find((int)$id);
        $target = TicketStatus::from($_POST['status']);
        $note = !empty($_POST['note']) ? trim($_POST['note']) : null;
        $techId = !empty($_POST['technician_id']) ? (int)$_POST['technician_id'] : null;

        try {
            $this->statusService->transition($ticket, $target, Auth::user(), $note, $techId);
            header("Location: /smart-it-helpdesk/public/tickets/{$id}");
        } catch (\DomainException $e) {
            echo "<script>alert('{$e->getMessage()}'); window.history.back();</script>";
        }
    }

    public function addComment(string $id): void
    {
        $imgPath = null;
        if (isset($_FILES['image'])) {
            $imgPath = FileUploader::upload($_FILES['image']);
        }
        $body = trim($_POST['body'] ?? '');
        if ($body !== '') {
            $this->repo->addComment((int)$id, Auth::id(), $body, $imgPath);
        }
        header("Location: /smart-it-helpdesk/public/tickets/{$id}");
    }

    public function rate(string $id): void
    {
        $score = (int)($_POST['score'] ?? 5);
        $feedback = trim($_POST['feedback'] ?? '');
        $this->repo->addRating((int)$id, $score, $feedback);
        
        $ticket = $this->repo->find((int)$id);
        
        // ส่งเป็น Instance Object ผ่าน TicketStatus::from()
        $this->statusService->transition($ticket, TicketStatus::from(TicketStatus::Closed), Auth::user(), 'ผู้ใช้ประเมินและปิดงาน');
        
        header("Location: /smart-it-helpdesk/public/tickets/{$id}");
    }
}