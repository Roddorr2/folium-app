<?php

namespace App\Infrastructure\Notifications;

use App\Domain\Contracts\NotifierInterface;

class WebSocketNotifier implements NotifierInterface
{
    public function notifyUser(string|int $userId, string $message, array $payload = []): bool
    {
        return $this->broadcast("private-user-{$userId}", 'NotificationEvent', [
            'message' => $message,
            'payload' => $payload,
            'timestamp' => time()
        ]);
    }

    public function broadcast(string $channel, string $event, array $data = []): bool
    {
        // Infrastructure implementation using WebSocket server (e.g., Laravel Reverb)
        return true;
    }
}
