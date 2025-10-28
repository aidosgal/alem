<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Applicant;
use App\Models\Manager;
use App\Models\Organization;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestChatController extends Controller
{
    /**
     * Create a test chat with a test applicant
     */
    public function createChat(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        try {
            DB::beginTransaction();

            // Create or get test user for applicant
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'password' => Hash::make('password'),
                    'role' => 'applicant',
                ]
            );

            // Create or get applicant
            $applicant = Applicant::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $request->name,
                    'last_name' => 'Test',
                ]
            );

            // Get first organization (or create test one)
            $organization = Organization::first();
            
            if (!$organization) {
                // Create test organization with manager
                $managerUser = User::firstOrCreate(
                    ['email' => 'test-manager@alem.kz'],
                    [
                        'name' => 'Test Manager',
                        'password' => Hash::make('password'),
                        'role' => 'manager',
                    ]
                );

                $manager = Manager::firstOrCreate(
                    ['user_id' => $managerUser->id],
                    [
                        'first_name' => 'Test',
                        'last_name' => 'Manager',
                        'phone' => '+7 777 777 7777',
                    ]
                );

                $organization = Organization::create([
                    'name' => 'Test Organization',
                    'description' => 'Test organization for WebSocket chat testing',
                    'manager_id' => $manager->id,
                    'address' => 'Almaty, Kazakhstan',
                    'phone' => '+7 777 777 7777',
                ]);
            }

            // Create or get chat
            $chat = Chat::firstOrCreate(
                [
                    'organization_id' => $organization->id,
                    'applicant_id' => $applicant->id,
                ],
                [
                    'last_message_at' => now(),
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Chat created successfully',
                'chat_id' => $chat->id,
                'applicant_id' => $applicant->id,
                'organization_id' => $organization->id,
                'applicant' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'organization' => [
                    'name' => $organization->name,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating chat: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get list of all chats for testing
     */
    public function getChats()
    {
        try {
            $chats = Chat::with(['applicant.user', 'organization'])
                ->withCount('messages')
                ->latest('last_message_at')
                ->limit(50)
                ->get()
                ->map(function ($chat) {
                    return [
                        'id' => $chat->id,
                        'applicant_id' => $chat->applicant_id,
                        'applicant_name' => $chat->applicant->user->name ?? 'Unknown',
                        'organization_name' => $chat->organization->name ?? 'Unknown',
                        'messages_count' => $chat->messages_count,
                        'last_message_at' => $chat->last_message_at?->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'chats' => $chats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading chats: ' . $e->getMessage(),
            ], 500);
        }
    }
}
