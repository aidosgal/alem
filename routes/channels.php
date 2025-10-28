<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channel - only users who belong to the chat can access
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = Chat::find($chatId);
    
    if (!$chat) {
        return false;
    }

    // Check if user is manager of the organization or applicant in the chat
    if ($user->manager) {
        $organization = $user->manager->currentOrganization();
        return $organization && $chat->organization_id === $organization->id;
    }

    if ($user->applicant) {
        return $chat->applicant_id === $user->applicant->id;
    }

    return false;
});

// Public test channel (only for development/testing)
if (config('app.env') === 'local') {
    Broadcast::channel('test-chat.{chatId}', function () {
        return true; // Allow all connections in local environment
    });
}
