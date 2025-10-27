<?php

use App\Http\Controllers\Manager\AuthController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\OrganizationController;
use App\Http\Controllers\Manager\VacancyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
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

        // Organization selection (for managers without organization)
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
        });
    });
});
