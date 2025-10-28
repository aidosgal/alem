<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Get list of chats for applicant
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        if (!$applicant) {
            return response()->json([
                'success' => false,
                'message' => 'Applicant profile not found'
            ], 404);
        }

        $chats = Chat::where('applicant_id', $applicant->id)
            ->with(['organization', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'chats' => $chats->map(function($chat) use ($applicant) {
                    $lastMessage = $chat->messages->first();
                    $unreadCount = $chat->messages()
                        ->where('sender_type', 'manager')
                        ->where('is_read', false)
                        ->count();

                    return [
                        'id' => $chat->id,
                        'organization' => [
                            'id' => $chat->organization->id,
                            'name' => $chat->organization->name,
                            'logo' => $chat->organization->logo,
                        ],
                        'last_message' => $lastMessage ? [
                            'id' => $lastMessage->id,
                            'content' => $lastMessage->content,
                            'sender_type' => $lastMessage->sender_type,
                            'has_attachments' => $lastMessage->attachments()->exists(),
                            'created_at' => $lastMessage->created_at->toISOString(),
                        ] : null,
                        'unread_count' => $unreadCount,
                        'updated_at' => $chat->updated_at->toISOString(),
                    ];
                })
            ]
        ], 200);
    }

    /**
     * Get or create chat with organization
     */
    public function getOrCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|exists:organizations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $applicant = $user->applicant;

        $chat = Chat::firstOrCreate([
            'applicant_id' => $applicant->id,
            'organization_id' => $request->organization_id,
        ]);

        $chat->load('organization');

        return response()->json([
            'success' => true,
            'data' => [
                'chat' => [
                    'id' => $chat->id,
                    'organization' => [
                        'id' => $chat->organization->id,
                        'name' => $chat->organization->name,
                        'logo' => $chat->organization->logo,
                    ],
                    'created_at' => $chat->created_at->toISOString(),
                ]
            ]
        ], 200);
    }

    /**
     * Get messages for a chat
     */
    public function messages($chatId, Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        $chat = Chat::where('id', $chatId)
            ->where('applicant_id', $applicant->id)
            ->first();

        if (!$chat) {
            return response()->json([
                'success' => false,
                'message' => 'Chat not found'
            ], 404);
        }

        // Mark messages as read
        $chat->messages()
            ->where('sender_type', 'manager')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get messages with pagination
        $perPage = $request->get('per_page', 50);
        $messages = $chat->messages()
            ->with(['attachments', 'repliedTo.attachments'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages->map(function($message) {
                    return [
                        'id' => $message->id,
                        'content' => $message->content,
                        'sender_type' => $message->sender_type,
                        'is_read' => $message->is_read,
                        'replied_to' => $message->replied_to_id ? [
                            'id' => $message->repliedTo->id,
                            'content' => $message->repliedTo->content,
                            'sender_type' => $message->repliedTo->sender_type,
                            'attachments' => $message->repliedTo->attachments->map(function($att) {
                                return [
                                    'id' => $att->id,
                                    'file_path' => $att->file_path,
                                    'file_url' => Storage::url($att->file_path),
                                    'file_type' => $att->file_type,
                                    'file_name' => $att->file_name,
                                ];
                            }),
                        ] : null,
                        'attachments' => $message->attachments->map(function($attachment) {
                            return [
                                'id' => $attachment->id,
                                'file_path' => $attachment->file_path,
                                'file_url' => Storage::url($attachment->file_path),
                                'file_type' => $attachment->file_type,
                                'file_name' => $attachment->file_name,
                            ];
                        }),
                        'created_at' => $message->created_at->toISOString(),
                    ];
                }),
                'pagination' => [
                    'total' => $messages->total(),
                    'per_page' => $messages->perPage(),
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                ]
            ]
        ], 200);
    }

    /**
     * Send a message
     */
    public function sendMessage($chatId, Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        $chat = Chat::where('id', $chatId)
            ->where('applicant_id', $applicant->id)
            ->first();

        if (!$chat) {
            return response()->json([
                'success' => false,
                'message' => 'Chat not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required_without:attachments|nullable|string',
            'replied_to_id' => 'nullable|exists:messages,id',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify replied_to message belongs to this chat
        if ($request->replied_to_id) {
            $repliedMessage = Message::where('id', $request->replied_to_id)
                ->where('chat_id', $chatId)
                ->first();
            
            if (!$repliedMessage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Replied message not found in this chat'
                ], 404);
            }
        }

        // Create message
        $message = Message::create([
            'chat_id' => $chat->id,
            'content' => $request->content,
            'sender_type' => 'applicant',
            'sender_id' => $applicant->id,
            'replied_to_id' => $request->replied_to_id,
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('chat-attachments', 'public');
                
                MessageAttachment::create([
                    'message_id' => $message->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }

        // Update chat timestamp
        $chat->touch();

        // Load relationships for response
        $message->load(['attachments', 'repliedTo.attachments']);

        // Broadcast message via WebSocket
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'sender_type' => $message->sender_type,
                    'is_read' => $message->is_read,
                    'replied_to' => $message->replied_to_id ? [
                        'id' => $message->repliedTo->id,
                        'content' => $message->repliedTo->content,
                        'sender_type' => $message->repliedTo->sender_type,
                        'attachments' => $message->repliedTo->attachments->map(function($att) {
                            return [
                                'id' => $att->id,
                                'file_path' => $att->file_path,
                                'file_url' => Storage::url($att->file_path),
                                'file_type' => $att->file_type,
                                'file_name' => $att->file_name,
                            ];
                        }),
                    ] : null,
                    'attachments' => $message->attachments->map(function($attachment) {
                        return [
                            'id' => $attachment->id,
                            'file_path' => $attachment->file_path,
                            'file_url' => Storage::url($attachment->file_path),
                            'file_type' => $attachment->file_type,
                            'file_name' => $attachment->file_name,
                        ];
                    }),
                    'created_at' => $message->created_at->toISOString(),
                ]
            ]
        ], 201);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead($chatId, Request $request)
    {
        $user = $request->user();
        $applicant = $user->applicant;

        $chat = Chat::where('id', $chatId)
            ->where('applicant_id', $applicant->id)
            ->first();

        if (!$chat) {
            return response()->json([
                'success' => false,
                'message' => 'Chat not found'
            ], 404);
        }

        $chat->messages()
            ->where('sender_type', 'manager')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read'
        ], 200);
    }
}
