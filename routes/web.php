<?php

use App\Http\Controllers\Manager\AuthController;
use App\Http\Controllers\Manager\ChatController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\OrderController;
use App\Http\Controllers\Manager\OrderStatusController;
use App\Http\Controllers\Manager\OrganizationController;
use App\Http\Controllers\Manager\OrganizationSwitchController;
use App\Http\Controllers\Manager\ProfileController;
use App\Http\Controllers\Manager\ServiceController;
use App\Http\Controllers\Manager\VacancyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('manager.dashboard');
    }
    return redirect()->route('manager.login');
});

// Manager Authentication Routes
Route::prefix('manager')->name('manager.')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
    });

    // Authenticated routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Profile management
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Organization management
        Route::get('/organizations', [OrganizationSwitchController::class, 'index'])->name('organizations.index');
        Route::post('/organizations/{id}/switch', [OrganizationSwitchController::class, 'switch'])->name('organizations.switch');
        Route::get('/organizations/create', [OrganizationSwitchController::class, 'create'])->name('organizations.create');
        Route::post('/organizations', [OrganizationSwitchController::class, 'store'])->name('organizations.store');
        Route::get('/organizations/join', [OrganizationSwitchController::class, 'joinForm'])->name('organizations.join');
        Route::post('/organizations/join', [OrganizationSwitchController::class, 'join'])->name('organizations.join.submit');

        // Organization selection (for managers without organization) - kept for compatibility
        Route::get('/organization/select', [OrganizationController::class, 'select'])->name('organization.select');
        Route::get('/organization/create', [OrganizationController::class, 'showCreateForm'])->name('organization.create');
        Route::post('/organization/create', [OrganizationController::class, 'create'])->name('organization.store');
        Route::get('/organization/join', [OrganizationController::class, 'showJoinForm'])->name('organization.join');
        Route::post('/organization/join', [OrganizationController::class, 'join'])->name('organization.join.store');

        // Routes that require organization membership
        Route::middleware(\App\Http\Middleware\EnsureManagerHasOrganization::class)->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::post('/organization/switch', [OrganizationController::class, 'switch'])->name('organization.switch');
            
            // Vacancy management
            Route::resource('vacancies', VacancyController::class);
            
            // Service management
            Route::resource('services', ServiceController::class);
            
            // Order management (Kanban board)
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
            
            // Order Status management
            Route::resource('order-statuses', OrderStatusController::class)->except(['show']);
            Route::post('/order-statuses/initialize', [OrderStatusController::class, 'initializeDefaults'])->name('order-statuses.initialize');
            
            // Chat management
            Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
            Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
            Route::post('/chat/{id}/message', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');
            Route::get('/chat/{id}/messages', [ChatController::class, 'loadMessages'])->name('chat.loadMessages');
            Route::post('/chat/{id}/order', [ChatController::class, 'createOrder'])->name('chat.createOrder');
            Route::get('/chat/message/{messageId}/download', [ChatController::class, 'downloadFile'])->name('chat.downloadFile');
        });
    });
});
