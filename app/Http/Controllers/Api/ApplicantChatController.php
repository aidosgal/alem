<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicantChatController extends Controller
{
    /**
     * Send a message from applicant (for testing)
     */
    public function sendMessage(Request $request, string $chatId)
    {
        $request->validate([
            'content' => 'nullable|string|max:10000',
            'applicant_id' => 'required|uuid|exists:applicants,id',
        ]);

        try {
            $chat = Chat::findOrFail($chatId);
            
            // Verify applicant belongs to this chat
            if ($chat->applicant_id !== $request->applicant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Applicant does not belong to this chat',
                ], 403);
            }

            DB::beginTransaction();

            // Create message
            $message = Message::create([
                'chat_id' => $chat->id,
                'content' => $request->content,
                'type' => 'text',
                'sender_applicant_id' => $request->applicant_id,
            ]);

            // Update chat last message time
            $chat->update(['last_message_at' => now()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message->load(['senderApplicant.user']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get chat messages
     */
    public function getMessages(Request $request, string $chatId)
    {
        try {
            $chat = Chat::findOrFail($chatId);
            
            $messages = Message::where('chat_id', $chat->id)
                ->with(['senderApplicant.user', 'senderManager'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->reverse()
                ->values();

            return response()->json([
                'success' => true,
                'messages' => $messages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading messages: ' . $e->getMessage(),
            ], 500);
        }
    }
}
