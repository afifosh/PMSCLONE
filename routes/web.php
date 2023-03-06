<?php

use App\Http\Controllers\Auth\ExpiredPasswordController;
use App\Http\Controllers\Auth\LockModeController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\Company\DashboardController;
use App\Http\Controllers\company\InvitationController;
use App\Http\Controllers\Company\UserAccountController;
use App\Http\Controllers\Company\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\CheckForLockMode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitation.accept')->middleware('guest' ,'guest:admin', 'guest:web');
Route::post('/invitations/{token}/confirm', [InvitationController::class, 'acceptConfirm'])->name('invitation.confirm')->middleware('guest' ,'guest:admin', 'guest:web');
Route::any('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');
Route::middleware('auth', 'verified', 'mustBeActive', CheckForLockMode::class)->group(function () {

    Route::post('/keep-alive', fn() => response()->json(['status' => __('success')]))->name('alive');
    Route::prefix('auth')->name('auth.')->group(function() {
        Route::get('lock', [LockModeController::class, 'lock'])->name('lock');
        Route::post('unlock', [LockModeController::class ,'unlock'])->name('unlock');
    });

    Route::prefix('password')->name('password.expired.')->group(function () {
        Route::view('expired', 'auth.expired-password');
        Route::post('expired', [ExpiredPasswordController::class, 'resetPassword'])->name('reset');
    });

    Route::middleware('passwordMustNotBeExpired')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('pages-home');
        Route::resource('user-account', UserAccountController::class);

    Route::get('users/roles/{role}', [UserController::class, 'showRole']);
    Route::resource('users', UserController::class);

    Route::get('company-profile', [CompanyProfileController::class, 'editDetails'])->name('company.editDetails');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('notification/count', [NotificationController::class, 'updateNotificationCount'])->name('notifications.count');
    Route::get('/pages/account-settings-notifications', [NotificationController::class, 'notificationTable'])->name('account.settings.notifications');


  });
});

require __DIR__.'/admin/admin.php';
