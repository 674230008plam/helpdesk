<?php
namespace App\Observers;

use App\Notifications\NotificationChannelInterface;

class TicketObserver
{
    private NotificationChannelInterface $notifier;

    public function __construct(NotificationChannelInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function handleCreated(array $ticket, string $adminEmail): void
    {
        $this->notifier->send(
            $adminEmail,
            "[แจ้งซ่อมใหม่] Ticket #{$ticket['id']}: {$ticket['title']}",
            "มีรายการแจ้งซ่อมใหม่: {$ticket['description']}\nระดับความสำคัญ: {$ticket['priority']}"
        );
    }

    public function handleStatusChanged(array $ticket, string $from, string $to, string $targetEmail): void
    {
        $this->notifier->send(
            $targetEmail,
            "[สถานะงานซ่อมเปลี่ยน] Ticket #{$ticket['id']} -> {$to}",
            "สถานะงานซ่อมของท่านในรายการ '{$ticket['title']}' ได้เปลี่ยนจาก [{$from}] เป็น [{$to}] แล้ว"
        );
    }
}
