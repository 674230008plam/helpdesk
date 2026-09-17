<?php
namespace App\Notifications;

class EmailNotificationService implements NotificationChannelInterface
{
    public function send(string $recipient, string $subject, string $message): bool
    {
        $logFile = dirname(__DIR__, 2) . '/storage/logs/app.log';
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) mkdir($logDir, 0777, true);

        $logEntry = sprintf(
            "[%s] [EMAIL NOTIFY] To: %s | Subject: %s | Body: %s\n",
            date('Y-m-d H:i:s'),
            $recipient,
            $subject,
            str_replace("\n", " ", $message)
        );
        file_put_contents($logFile, $logEntry, FILE_APPEND);

        $headers = "From: IT Helpdesk <noreply@helpdesk.local>\r\nContent-Type: text/plain; charset=UTF-8";
        @mail($recipient, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $headers);
        return true;
    }
}
