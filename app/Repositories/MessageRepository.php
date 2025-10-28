<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class MessageRepository
{
    /**
     * Get messages for a chat.
     */
    public function getChatMessages(Chat $chat, int $limit = 50, ?string $beforeId = null): Collection
    {
        $query = Message::where('chat_id', $chat->id)
            ->with([
                'senderApplicant.user',
                'senderManager',
                'replyTo',
            ])
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($beforeId) {
            $beforeMessage = Message::find($beforeId);
            if ($beforeMessage) {
                $query->where('created_at', '<', $beforeMessage->created_at);
            }
        }

        return $query->get()->reverse()->values();
    }

    /**
     * Create a new message.
     */
    public function create(array $data): Message
    {
        return Message::create($data);
    }

    /**
     * Find message by ID.
     */
    public function findById(string $id): ?Message
    {
        return Message::find($id);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message): Message
    {
        if (!$message->read_at) {
            $message->update(['read_at' => now()]);
        }
        return $message->fresh();
    }

    /**
     * Mark all chat messages from applicant as read.
     */
    public function markChatMessagesAsRead(Chat $chat): int
    {
        return Message::where('chat_id', $chat->id)
            ->whereNotNull('sender_applicant_id')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Store uploaded file.
     */
    public function storeFile($file, string $chatId): array
    {
        $originalName = $file->getClientOriginalName();
        $path = $file->store("chats/{$chatId}", 'public');
        $size = $file->getSize();

        return [
            'path' => $path,
            'name' => $originalName,
            'size' => $size,
        ];
    }

    /**
     * Delete file from storage.
     */
    public function deleteFile(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Get unread count for organization in chat.
     */
    public function getUnreadCountForOrganization(Chat $chat): int
    {
        return Message::where('chat_id', $chat->id)
            ->whereNotNull('sender_applicant_id')
            ->whereNull('read_at')
            ->count();
    }
}
