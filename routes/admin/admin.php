<?php

use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\Company\ContactPersonController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyRoleController;
use App\Http\Controllers\Admin\Partner\DepartmentController;
use App\Http\Controllers\Admin\Partner\DesignationController;
use App\Http\Controllers\Admin\Partner\PatnerCompanyController;
use App\Http\Controllers\Admin\Program\ProgramController;
use App\Http\Controllers\Auth\ExpiredPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:admin', 'adminVerified')->group(function () {

    Route::prefix('password')->name('password.expired.')->group(function () {
      Route::view('expired', 'admin.auth.expired-password');
      Route::post('expired', [ExpiredPasswordController::class, 'resetPassword'])->name('reset');
    });

    Route::middleware('passwordMustNotBeExpired:admin')->group(function() {
      Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

      Route::resource('admin-account', AdminAccountController::class)->only('edit');

      Route::resource('roles', AdminRoleController::class);

      Route::resource('company-roles', CompanyRoleController::class);

      Route::get('/users/{admin}/impersonate', [AdminUsersController::class, 'impersonate'])->name('impersonate-admin');
      Route::get('/users/leave-impersonate', [AdminUsersController::class, 'leaveImpersonate'])->name('leave-impersonate');
      Route::resource('users', AdminUsersController::class);

      Route::resource('companies', CompanyController::class);
      Route::resource('companies.contact-persons', ContactPersonController::class);

      Route::prefix('partner')->name('partner.')->group(function() {
        Route::resource('companies', PatnerCompanyController::class);
        Route::get('departments/get-by-company', [DepartmentController::class, 'getByComapnyId'])->name('departments.getByCompany');
        Route::resource('departments', DepartmentController::class);
        Route::get('designations/get-by-department', [DesignationController::class, 'getByDepartmentId'])->name('designations.getByDepartment');
        Route::resource('designations', DesignationController::class);
      });

    Route::resource('programs', ProgramController::class);
    // Route::resource('companies.invitations', InvitationController::class);
    Route::view('settings', 'admin.app-setting')->name('settings.index');

    Route::post('/keep-alive', fn() => response()->json(['status' => __('success')]));
  });
});

require __DIR__.'/auth.php';
