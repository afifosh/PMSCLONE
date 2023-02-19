<?php

use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\Company\ContactPersonController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyRoleController;
use App\Http\Controllers\Admin\EmailServiceController;
use App\Http\Controllers\Admin\Partner\DepartmentController;
use App\Http\Controllers\Admin\Partner\DesignationController;
use App\Http\Controllers\Admin\Partner\PatnerCompanyController;
use App\Http\Controllers\Admin\Program\ProgramController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\ExpiredPasswordController;
use App\Http\Controllers\Auth\LockModeController;
use App\Http\Middleware\CheckForLockMode;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin', 'adminVerified', CheckForLockMode::class)->group(function () {

  Route::post('/keep-alive', fn () => response()->json(['status' => __('success')]))->name('alive');
  Route::prefix('auth')->name('auth.')->group(function() {
    Route::get('lock', [LockModeController::class, 'lock'])->name('lock');
    Route::post('unlock', [LockModeController::class ,'unlock'])->name('unlock');
  });

  Route::prefix('password')->name('password.expired.')->group(function () {
    Route::view('expired', 'admin.auth.expired-password');
    Route::post('expired', [ExpiredPasswordController::class, 'resetPassword'])->name('reset');
  });

  Route::middleware('passwordMustNotBeExpired')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::resource('admin-account', AdminAccountController::class)->only('edit');
    Route::get('admin-account-auth-logs', [AdminAccountController::class, 'authLogs'])->name('auth-logs');

    Route::get('/pages/account-settings-notifications', [NotificationController::class, 'notificationTable'])->name('account.settings.notifications');

    Route::resource('roles', AdminRoleController::class);

    Route::resource('company-roles', CompanyRoleController::class);

    Route::get('/users/{admin}/impersonate', [AdminUsersController::class, 'impersonate'])->name('impersonate-admin');
    Route::get('/users/leave-impersonate', [AdminUsersController::class, 'leaveImpersonate'])->name('leave-impersonate');
    Route::resource('users', AdminUsersController::class);

    Route::resource('companies', CompanyController::class);
    Route::resource('companies.contact-persons', ContactPersonController::class);

    Route::prefix('partner')->name('partner.')->group(function () {
      Route::resource('companies', PatnerCompanyController::class);
      Route::get('departments/get-by-company', [DepartmentController::class, 'getByComapnyId'])->name('departments.getByCompany');
      Route::resource('departments', DepartmentController::class);
      Route::get('designations/get-by-department', [DesignationController::class, 'getByDepartmentId'])->name('designations.getByDepartment');
      Route::resource('designations', DesignationController::class);
    });

    Route::resource('programs', ProgramController::class);
    // Route::resource('companies.invitations', InvitationController::class);
    Route::prefix('settings')->name('setting.')->group(function () {
      Route::get('/', [AppSettingController::class, 'index'])->name('index');
      Route::post('general', [AppSettingController::class, 'storeGeneralSettings'])->name('store');
      Route::post('email', [EmailServiceController::class, 'upsert'])->name('email.upsert');
    });


    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('notifications/count', [NotificationController::class, 'updateNotificationCount'])->name('notifications.count');
  });
});

require __DIR__ . '/auth.php';
