<?php

namespace App\Services;

use App\Core\EventDispatcher;
use App\Enums\TicketStatus;
use App\Repositories\TicketRepository;

class TicketStatusService
{
    private TicketRepository $repo;
    private EventDispatcher $events;

    public function __construct(TicketRepository $repo, EventDispatcher $events)
    {
        $this->repo = $repo;
        $this->events = $events;
    }

    public function transition(array $ticket, TicketStatus $target, array $actor, ?string $note = null, ?int $techId = null): void
    {
        $from = TicketStatus::from($ticket['status']);
        $this->validateTransition($from, $target);
        $this->checkPermission($actor, $from, $target);

        $update = ['status' => $target->value];
        if ($target->value === TicketStatus::Assigned && $techId) {
            $update['technician_id'] = $techId;
        } elseif ($target->value === TicketStatus::Resolved) {
            $update['resolved_at'] = date('Y-m-d H:i:s');
        } elseif ($target->value === TicketStatus::Closed) {
            $update['closed_at'] = date('Y-m-d H:i:s');
        }

        $this->repo->update((int)$ticket['id'], $update);
        $this->repo->logStatus((int)$ticket['id'], (int)$actor['id'], $from->value, $target->value, $note);

        $this->events->dispatch('ticket.status_changed', [
            'ticket' => $ticket,
            'from' => $from->value,
            'to' => $target->value,
            'actor' => $actor
        ]);
    }

    private function validateTransition(TicketStatus $from, TicketStatus $to): void
    {
        $transitions = [
            TicketStatus::Open => [TicketStatus::Assigned],
            TicketStatus::Assigned => [TicketStatus::InProgress],
            TicketStatus::InProgress => [TicketStatus::Resolved],
            TicketStatus::Resolved => [TicketStatus::Closed, TicketStatus::InProgress],
            TicketStatus::Closed => [],
        ];

        $allowed = $transitions[$from->value] ?? [];

        if (!in_array($to->value, $allowed, true)) {
            throw new \DomainException("ไม่สามารถเปลี่ยนสถานะจาก {$from->value} ไปเป็น {$to->value} ได้");
        }
    }

    private function checkPermission(array $actor, TicketStatus $from, TicketStatus $to): void
    {
        $role = $actor['role'];
        if ($from->value === TicketStatus::Open && $to->value === TicketStatus::Assigned && $role !== 'admin') {
            throw new \DomainException("เฉพาะ Admin เท่านั้นที่มอบหมายงานให้ช่างได้");
        }
        if ($from->value === TicketStatus::Assigned && $to->value === TicketStatus::InProgress && !in_array($role, ['technician', 'admin'])) {
            throw new \DomainException("เฉพาะช่างเทคนิคเท่านั้นที่สามารถกดรับงานได้");
        }
        if ($from->value === TicketStatus::InProgress && $to->value === TicketStatus::Resolved && !in_array($role, ['technician', 'admin'])) {
            throw new \DomainException("เฉพาะช่างเทคนิคเท่านั้นที่แจ้งซ่อมเสร็จได้");
        }
        if ($from->value === TicketStatus::Resolved && in_array($to->value, [TicketStatus::Closed, TicketStatus::InProgress]) && !in_array($role, ['user', 'admin'])) {
            throw new \DomainException("เฉพาะเจ้าของงานซ่อมเท่านั้นที่ยืนยันปิดงานหรือส่งให้ซ่อมใหม่");
        }
    }
}