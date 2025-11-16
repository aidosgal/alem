<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebSocketNotifier
{
    protected string $wsUrl;

    public function __construct()
    {
        $this->wsUrl = config('app.websocket_url', 'wss://ws.azed.kz');
        // Convert ws:// to http:// and wss:// to https:// for HTTP requests to Go server
        $this->wsUrl = str_replace('ws://', 'http://', $this->wsUrl);
        $this->wsUrl = str_replace('wss://', 'https://', $this->wsUrl);
    }

    /**
     * Notify all users in a chat room
     */
    public function notifyChat(string $chatId): void
    {
        try {
            // Send HTTP request to Go server to broadcast to chat room
            Http::timeout(2)->post("{$this->wsUrl}/notify/chat/{$chatId}");
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
            // Send HTTP request to Go server to broadcast to user
            Http::timeout(2)->post("{$this->wsUrl}/notify/user/{$userId}");
            Log::info("WebSocket notification sent to user: {$userId}");
        } catch (\Exception $e) {
            Log::warning("Failed to send WebSocket notification to user {$userId}: {$e->getMessage()}");
        }
    }
}
