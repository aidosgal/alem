<?php

use App\Http\Controllers\Api\Manager\ManagerOrderApiController;
use App\Http\Controllers\Api\TestChatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VacancyController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\OrganizationController;
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

// Applicant Mobile API routes
Route::prefix('v1')->name('api.v1.')->group(function () {
    
    // Public routes (no authentication required)
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    // Public vacancies
    Route::prefix('vacancies')->name('vacancies.')->group(function () {
        Route::get('/', [VacancyController::class, 'index'])->name('index');
        Route::get('/{id}', [VacancyController::class, 'show'])->name('show');
        Route::get('/filters/cities', [VacancyController::class, 'cities'])->name('cities');
        Route::get('/filters/types', [VacancyController::class, 'types'])->name('types');
    });

    // Public services
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::get('/{id}', [ServiceController::class, 'show'])->name('show');
        Route::get('/filters/categories', [ServiceController::class, 'categories'])->name('categories');
    });

    // Public organizations
    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('/{id}', [OrganizationController::class, 'show'])->name('show');
    });

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', [AuthController::class, 'me'])->name('me');
            Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        });

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');
            Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('avatar');
            Route::post('/documents', [ProfileController::class, 'uploadDocument'])->name('documents.upload');
            Route::delete('/documents/{id}', [ProfileController::class, 'deleteDocument'])->name('documents.delete');
        });

        // Chat
        Route::prefix('chats')->name('chats.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::post('/get-or-create', [ChatController::class, 'getOrCreate'])->name('getOrCreate');
            Route::get('/{chatId}/messages', [ChatController::class, 'messages'])->name('messages');
            Route::post('/{chatId}/messages', [ChatController::class, 'sendMessage'])->name('sendMessage');
            Route::post('/{chatId}/mark-read', [ChatController::class, 'markAsRead'])->name('markRead');
        });

        // Orders (read-only)
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
            Route::get('/filters/statuses', [OrderController::class, 'statuses'])->name('statuses');
        });
    });
});

// Test API routes
Route::prefix('test')->name('api.test.')->group(function () {
    Route::post('/create-chat', [TestChatController::class, 'createChat'])->name('create-chat');
    Route::get('/chats', [TestChatController::class, 'getChats'])->name('chats');
});

// Applicant chat API routes (for testing without auth)
Route::prefix('applicant')->name('api.applicant.')->group(function () {
    Route::post('/chat/{chatId}/messages', [\App\Http\Controllers\Api\ApplicantChatController::class, 'sendMessage'])->name('send-message');
    Route::get('/chat/{chatId}/messages', [\App\Http\Controllers\Api\ApplicantChatController::class, 'getMessages'])->name('get-messages');
});
