<?php

use App\Http\Controllers\Api\Manager\ManagerOrderApiController;
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
