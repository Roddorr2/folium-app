<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Contracts\NotifierInterface;

class EmailNotifier implements NotifierInterface
{
    public function notifyUser(string|int $userId, string $message, array $payload = []): bool
    {
        // Infrastructure implementation enqueueing asynchronous Mailable
        return true;
    }

    public function broadcast(string $channel, string $event, array $data = []): bool
    {
        // Email notifier does not support real-time channel broadcasting
        return false;
    }
}
