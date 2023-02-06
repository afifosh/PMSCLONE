<?php

use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\Company\ContactPersonController;
use App\Http\Controllers\Admin\Company\InvitationController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin', 'adminVerified')->group(function () {

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::resource('admin-account', AdminAccountController::class)->only('edit');

    Route::resource('roles', AdminRoleController::class);

    Route::resource('company-roles', CompanyRoleController::class);

    Route::get('/users/{admin}/impersonate', [AdminUsersController::class, 'impersonate'])->name('impersonate-admin');
    Route::get('/users/leave-impersonate', [AdminUsersController::class, 'leaveImpersonate'])->name('leave-impersonate');
    Route::resource('users', AdminUsersController::class);

    Route::resource('companies', CompanyController::class);
    Route::resource('companies.contact-persons', ContactPersonController::class);
    // Route::resource('companies.invitations', InvitationController::class);
});

require __DIR__.'/auth.php';
