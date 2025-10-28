<?php

use App\Http\Controllers\Api\Manager\ManagerOrderApiController;
use App\Http\Controllers\Api\TestChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Manager API routes
Route::prefix('manager')->name('api.manager.')->middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [ManagerOrderApiController::class, 'index'])->name('orders.index');
    Route::get('/order-statuses', [ManagerOrderApiController::class, 'getStatuses'])->name('orders.statuses');
    Route::patch('/orders/{id}/status', [ManagerOrderApiController::class, 'updateStatus'])->name('orders.updateStatus');
});

// Test API routes (for testing WebSocket chat)
Route::prefix('test')->name('api.test.')->group(function () {
    Route::post('/create-chat', [TestChatController::class, 'createChat'])->name('create-chat');
    Route::get('/chats', [TestChatController::class, 'getChats'])->name('chats');
});

// Applicant chat API routes (for testing without auth)
Route::prefix('applicant')->name('api.applicant.')->group(function () {
    Route::post('/chat/{chatId}/messages', [\App\Http\Controllers\Api\ApplicantChatController::class, 'sendMessage'])->name('send-message');
    Route::get('/chat/{chatId}/messages', [\App\Http\Controllers\Api\ApplicantChatController::class, 'getMessages'])->name('get-messages');
});
