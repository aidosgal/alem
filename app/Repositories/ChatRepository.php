<?php

namespace App\Repositories;

use App\Models\Chat;
use App\Models\Organization;
use App\Models\Applicant;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository
{
    /**
     * Get all chats for an organization with latest message and unread count.
     */
    public function getOrganizationChats(Organization $organization): Collection
    {
        return Chat::where('organization_id', $organization->id)
            ->with([
                'applicant.user',
                'lastMessage',
                'order.orderStatus',
            ])
            ->withCount(['unreadMessagesForOrganization as unread_count'])
            ->orderByDesc('last_message_at')
            ->get();
    }

    /**
     * Find chat by ID.
     */
    public function findById(string $id): ?Chat
    {
        return Chat::with([
            'applicant.user',
            'organization',
            'order.orderStatus',
            'messages.senderApplicant.user',
            'messages.senderManager',
            'messages.replyTo',
        ])->find($id);
    }

    /**
     * Find or create chat between organization and applicant.
     */
    public function findOrCreate(Organization $organization, Applicant $applicant): Chat
    {
        return Chat::firstOrCreate(
            [
                'organization_id' => $organization->id,
                'applicant_id' => $applicant->id,
            ],
            [
                'last_message_at' => now(),
            ]
        );
    }

    /**
     * Update last message timestamp.
     */
    public function updateLastMessageAt(Chat $chat): Chat
    {
        $chat->update(['last_message_at' => now()]);
        return $chat->fresh();
    }

    /**
     * Check if chat belongs to organization.
     */
    public function belongsToOrganization(Chat $chat, Organization $organization): bool
    {
        return $chat->organization_id === $organization->id;
    }

    /**
     * Attach order to chat.
     */
    public function attachOrder(Chat $chat, string $orderId): Chat
    {
        $chat->update(['order_id' => $orderId]);
        return $chat->fresh();
    }
}
