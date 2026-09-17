<?php
namespace App\Notifications;

interface NotificationChannelInterface {
    public function send(string $recipient, string $subject, string $message): bool;
}
