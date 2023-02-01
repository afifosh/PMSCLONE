<?php

use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminAccountController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin', 'adminVerified')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('admin-account', AdminAccountController::class);
    Route::resource('roles', AdminRoleController::class);
    // Route::get('/user-management', [AdminController::class, 'adminManagement'])->name('admin-management');
    Route::resource('/users', UserController::class);
});

require __DIR__.'/auth.php';
