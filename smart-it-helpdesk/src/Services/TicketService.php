<?php
namespace App\Services;

use App\Core\EventDispatcher;
use App\Core\FileUploader;
use App\Repositories\TicketRepository;

class TicketService
{
    private TicketRepository $repo;
    private EventDispatcher $events;

    public function __construct(TicketRepository $repo, EventDispatcher $events)
    {
        $this->repo = $repo;
        $this->events = $events;
    }

    public function createTicket(array $data, ?array $file = null): array
    {
        $imagePath = null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $imagePath = FileUploader::upload($file);
        }

        $ticketId = $this->repo->create([
            'user_id' => $data['user_id'],
            'category_id' => (int)$data['category_id'],
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'priority' => $data['priority'],
            'image_path' => $imagePath,
        ]);

        $ticket = $this->repo->find($ticketId);
        $this->repo->logStatus($ticketId, (int)$data['user_id'], 'None', 'Open', 'User created ticket');
        $this->events->dispatch('ticket.created', $ticket);

        return $ticket;
    }
}
