<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use WebSocket\Client;

class WebSocketNotifier
{
    protected string $wsUrl;

    public function __construct()
    {
        $this->wsUrl = config('app.websocket_url', 'wss://ws.azed.kz');
    }

    /**
     * Notify all users in a chat room
     */
    public function notifyChat(string $chatId): void
    {
        try {
            $client = new Client($this->wsUrl . "/chat/{$chatId}");
            $client->send(json_encode([
                'type' => 'notify_chat',
                'target' => $chatId
            ]));
            $client->close();
            Log::info("WebSocket notification sent to chat: {$chatId}");
        } catch (\Exception $e) {
            Log::warning("Failed to send WebSocket notification to chat {$chatId}: {$e->getMessage()}");
        }
    }

    /**
     * Notify a specific user
     */
    public function notifyUser(string $userId): void
    {
        try {
            $client = new Client($this->wsUrl . "/user/{$userId}");
            $client->send(json_encode([
                'type' => 'notify_user',
                'target' => $userId
            ]));
            $client->close();
            Log::info("WebSocket notification sent to user: {$userId}");
        } catch (\Exception $e) {
            Log::warning("Failed to send WebSocket notification to user {$userId}: {$e->getMessage()}");
        }
    }
}
