<?php

namespace App\Domain\Contracts;

interface NotifierInterface
{
    /**
     * Send a notification to a specific user.
     *
     * @param string|int $userId
     * @param string $message
     * @param array<string, mixed> $payload
     * @return bool
     */
    public function notifyUser(string|int $userId, string $message, array $payload = []): bool;

    /**
     * Broadcast a notification to a channel or topic.
     *
     * @param string $channel
     * @param string $event
     * @param array<string, mixed> $data
     * @return bool
     */
    public function broadcast(string $channel, string $event, array $data = []): bool;
}
