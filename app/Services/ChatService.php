<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Applicant;
use App\Repositories\ChatRepository;
use App\Repositories\MessageRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class ChatService
{
    protected ChatRepository $chatRepository;
    protected MessageRepository $messageRepository;

    public function __construct(
        ChatRepository $chatRepository,
        MessageRepository $messageRepository
    ) {
        $this->chatRepository = $chatRepository;
        $this->messageRepository = $messageRepository;
    }

    /**
     * Get all chats for organization.
     */
    public function getOrganizationChats(Organization $organization)
    {
        return $this->chatRepository->getOrganizationChats($organization);
    }

    /**
     * Get chat by ID with permission check.
     */
    public function getChatById(string $id, Organization $organization): ?Chat
    {
        $chat = $this->chatRepository->findById($id);

        if (!$chat || !$this->chatRepository->belongsToOrganization($chat, $organization)) {
            return null;
        }

        return $chat;
    }

    /**
     * Get or create chat with applicant.
     */
    public function getOrCreateChat(Organization $organization, string $applicantId): Chat
    {
        $applicant = Applicant::findOrFail($applicantId);
        return $this->chatRepository->findOrCreate($organization, $applicant);
    }

    /**
     * Send message from manager.
     */
    public function sendMessageFromManager(
        Chat $chat,
        Organization $organization,
        string $managerId,
        array $data
    ): Message {
        if (!$this->chatRepository->belongsToOrganization($chat, $organization)) {
            throw new Exception('У вас нет доступа к этому чату.');
        }

        return DB::transaction(function () use ($chat, $managerId, $data) {
            // Handle file upload if present
            if (isset($data['file'])) {
                $fileData = $this->messageRepository->storeFile($data['file'], $chat->id);
                $data['file_path'] = $fileData['path'];
                $data['file_name'] = $fileData['name'];
                $data['file_size'] = $fileData['size'];
                $data['type'] = $this->getFileType($data['file']);
                unset($data['file']);
            }

            $message = $this->messageRepository->create([
                'chat_id' => $chat->id,
                'content' => $data['content'] ?? null,
                'type' => $data['type'] ?? 'text',
                'file_path' => $data['file_path'] ?? null,
                'file_name' => $data['file_name'] ?? null,
                'file_size' => $data['file_size'] ?? null,
                'metadata' => $data['metadata'] ?? [],
                'sender_organization_manager_id' => $managerId,
                'reply_to_message_id' => $data['reply_to'] ?? null,
            ]);

            $this->chatRepository->updateLastMessageAt($chat);

            // Broadcast message via WebSocket
            broadcast(new MessageSent($message))->toOthers();

            return $message;
        });
    }

    /**
     * Get chat messages with pagination.
     */
    public function getChatMessages(Chat $chat, Organization $organization, ?string $beforeId = null)
    {
        if (!$this->chatRepository->belongsToOrganization($chat, $organization)) {
            throw new Exception('У вас нет доступа к этому чату.');
        }

        return $this->messageRepository->getChatMessages($chat, 50, $beforeId);
    }

    /**
     * Mark all messages in chat as read.
     */
    public function markChatAsRead(Chat $chat, Organization $organization): int
    {
        if (!$this->chatRepository->belongsToOrganization($chat, $organization)) {
            throw new Exception('У вас нет доступа к этому чату.');
        }

        return $this->messageRepository->markChatMessagesAsRead($chat);
    }

    /**
     * Create order from chat.
     */
    public function createOrderFromChat(
        Chat $chat,
        Organization $organization,
        array $orderData
    ) {
        if (!$this->chatRepository->belongsToOrganization($chat, $organization)) {
            throw new Exception('У вас нет доступа к этому чату.');
        }

        \Log::info('Creating order from chat', [
            'chat_id' => $chat->id,
            'organization_id' => $organization->id,
            'order_data' => $orderData,
        ]);

        return DB::transaction(function () use ($chat, $orderData) {
            // Get default status if not provided
            $statusId = $orderData['status_id'] ?? null;
            if (!$statusId) {
                $defaultStatus = \App\Models\OrderStatus::where('organization_id', $chat->organization_id)
                    ->orderBy('order')
                    ->first();
                $statusId = $defaultStatus?->id;
                \Log::info('Using default status', ['status_id' => $statusId]);
            }

            // Create order
            \Log::info('Creating order with data', [
                'organization_id' => $chat->organization_id,
                'applicant_id' => $chat->applicant_id,
                'title' => $orderData['title'],
                'price' => $orderData['price'],
                'status_id' => $statusId,
            ]);

            $order = Order::create([
                'organization_id' => $chat->organization_id,
                'applicant_id' => $chat->applicant_id,
                'title' => $orderData['title'],
                'description' => $orderData['description'] ?? '',
                'price' => $orderData['price'],
                'deadline_at' => $orderData['deadline_at'] ?? null,
                'status_id' => $statusId,
            ]);

            \Log::info('Order created successfully', ['order_id' => $order->id]);

            // Attach services if provided
            if (!empty($orderData['service_ids'])) {
                \Log::info('Attaching services', ['service_ids' => $orderData['service_ids']]);
                $order->services()->attach($orderData['service_ids']);
            }

            // Link order to chat
            \Log::info('Linking order to chat');
            $this->chatRepository->attachOrder($chat, $order->id);

            // Send system message about order creation
            \Log::info('Creating system message');
            $message = $this->messageRepository->create([
                'chat_id' => $chat->id,
                'content' => 'Создан заказ: ' . $order->title,
                'type' => 'order',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_title' => $order->title,
                    'order_price' => $order->price,
                ],
                'sender_organization_manager_id' => $orderData['manager_id'],
            ]);

            $this->chatRepository->updateLastMessageAt($chat);
            
            \Log::info('Broadcasting message');
            broadcast(new MessageSent($message))->toOthers();

            \Log::info('Order creation completed', ['order_id' => $order->id]);
            return $order;
        });
    }

    /**
     * Determine file type from uploaded file.
     */
    protected function getFileType($file): string
    {
        $mimeType = $file->getMimeType();

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        return 'file';
    }
}
