<?php

namespace App\Domain\Contracts;

interface NotifierInterface
{
    /**
     * Send a notification to a specific user.
     *
     * @param  array<string, mixed>  $payload
     */
    public function notifyUser(string|int $userId, string $message, array $payload = []): bool;

    /**
     * Broadcast a notification to a channel or topic.
     *
     * @param  array<string, mixed>  $data
     */
    public function broadcast(string $channel, string $event, array $data = []): bool;
}
