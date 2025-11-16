<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ChatService;
use App\Services\OrderService;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    protected ChatService $chatService;
    protected OrderService $orderService;
    protected ServiceService $serviceService;

    public function __construct(
        ChatService $chatService,
        OrderService $orderService,
        ServiceService $serviceService
    ) {
        $this->chatService = $chatService;
        $this->orderService = $orderService;
        $this->serviceService = $serviceService;
    }

    /**
     * Display all chats.
     */
    public function index(Request $request)
    {
        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Выберите организацию.');
        }

        $chats = $this->chatService->getOrganizationChats($organization);

        return view('manager.chat.index', compact('chats'));
    }

    /**
     * Display specific chat.
     */
    public function show(Request $request, string $id)
    {
        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Выберите организацию.');
        }

        $chat = $this->chatService->getChatById($id, $organization);

        if (!$chat) {
            return redirect()->route('manager.chat.index')
                ->with('error', 'Чат не найден.');
        }

        // Mark messages as read
        $this->chatService->markChatAsRead($chat, $organization);

        $services = $this->serviceService->getOrganizationServices($organization);

        return view('manager.chat.show', compact('chat', 'services'));
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request, string $id)
    {
        $request->validate([
            'content' => 'nullable|string|max:10000',
            'file' => 'nullable|file|max:10240', // 10MB max
            'reply_to' => 'nullable|uuid|exists:messages,id',
        ], [
            'file.max' => 'Файл не должен превышать 10 МБ.',
        ]);

        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return response()->json(['error' => 'Выберите организацию.'], 403);
        }

        $chat = $this->chatService->getChatById($id, $organization);

        if (!$chat) {
            return response()->json(['error' => 'Чат не найден.'], 404);
        }

        try {
            $message = $this->chatService->sendMessageFromManager(
                $chat,
                $organization,
                $manager->id,
                [
                    'content' => $request->input('content', ''),
                    'file' => $request->file('file'),
                    'reply_to' => $request->input('reply_to'),
                ]
            );

            $message->load(['senderManager', 'replyTo']);
            
            return response()->json([
                'success' => true,
                'message' => $this->formatMessage($message),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Load more messages (pagination).
     */
    public function loadMessages(Request $request, string $id)
    {
        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            return response()->json(['error' => 'Выберите организацию.'], 403);
        }

        $chat = $this->chatService->getChatById($id, $organization);

        if (!$chat) {
            return response()->json(['error' => 'Чат не найден.'], 404);
        }

        // Check if polling for new messages (after_id parameter)
        if ($request->has('after_id')) {
            $afterId = $request->input('after_id');
            $newMessages = \App\Models\Message::where('chat_id', $chat->id)
                ->where('id', '>', $afterId)
                ->with(['senderApplicant.user', 'senderManager', 'replyTo'])
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'messages' => $newMessages->map(function($msg) {
                    return $this->formatMessage($msg);
                }),
            ]);
        }

        // Load older messages (pagination)
        $messages = $this->chatService->getChatMessages(
            $chat,
            $organization,
            $request->input('before_id')
        );

        return response()->json([
            'messages' => $messages,
            'has_more' => $messages->count() >= 50,
        ]);
    }

    /**
     * Create order from chat.
     */
    public function createOrder(Request $request, string $id)
    {
        \Log::info('ChatController::createOrder called', [
            'chat_id' => $id,
            'request_data' => $request->all(),
        ]);

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'deadline_at' => 'nullable|date|after:now',
                'service_ids' => 'nullable|array',
                'service_ids.*' => 'exists:services,id',
                'status_id' => 'nullable|exists:order_statuses,id',
            ], [
                'title.required' => 'Введите название заказа.',
                'price.required' => 'Укажите цену.',
                'price.min' => 'Цена не может быть отрицательной.',
                'deadline_at.after' => 'Срок должен быть в будущем.',
            ]);

            \Log::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        \Log::info('Manager and organization retrieved', [
            'manager_id' => $manager->id,
            'organization_id' => $organization?->id,
        ]);

        if (!$organization) {
            \Log::error('No organization selected');
            return response()->json(['error' => 'Выберите организацию.'], 403);
        }

        $chat = $this->chatService->getChatById($id, $organization);

        if (!$chat) {
            \Log::error('Chat not found', ['chat_id' => $id]);
            return response()->json(['error' => 'Чат не найден.'], 404);
        }

        \Log::info('Chat found', ['chat_id' => $chat->id]);

        try {
            $order = $this->chatService->createOrderFromChat(
                $chat,
                $organization,
                array_merge($request->all(), ['manager_id' => $manager->id])
            );

            \Log::info('Order created successfully in controller', ['order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'order' => $order->load('orderStatus', 'services'),
                'message' => 'Заказ успешно создан.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Download file.
     */
    public function downloadFile(Request $request, string $messageId)
    {
        $manager = $request->user()->manager;
        $organization = $manager->currentOrganization();

        if (!$organization) {
            abort(403);
        }

        $message = \App\Models\Message::findOrFail($messageId);
        $chat = $message->chat;

        if ($chat->organization_id !== $organization->id) {
            abort(403);
        }

        if (!$message->file_path) {
            abort(404);
        }

            return Storage::disk('public')->download(
                $message->file_path,
                $message->file_name
            );
    }

    /**
     * Format message for JSON response.
     */
    protected function formatMessage($message)
    {
        return [
            'id' => $message->id,
            'chat_id' => $message->chat_id,
            'content' => $message->content,
            'type' => $message->type,
            'file_path' => $message->file_path,
            'file_name' => $message->file_name,
            'file_size' => $message->file_size,
            'metadata' => $message->metadata,
            'sender' => [
                'type' => $message->sender_type,
                'id' => $message->sender_applicant_id ?: $message->sender_organization_manager_id,
                'name' => $message->isFromApplicant() 
                    ? ($message->senderApplicant->user->name ?? 'Applicant')
                    : ($message->senderManager->full_name ?? 'Manager'),
            ],
            'reply_to' => $message->replyTo ? [
                'id' => $message->replyTo->id,
                'content' => $message->replyTo->content,
                'sender_name' => $message->replyTo->sender_name,
            ] : null,
            'created_at' => $message->created_at->toIso8601String(),
            'read_at' => $message->read_at?->toIso8601String(),
        ];
    }
}