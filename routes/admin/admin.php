<?php

use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('roles', AdminRoleController::class);
    // Route::get('/user-management', [AdminController::class, 'adminManagement'])->name('admin-management');
    Route::resource('/users', UserController::class);
});

require __DIR__.'/auth.php';
