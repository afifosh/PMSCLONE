<?php

use App\Http\Controllers\Auth\ExpiredPasswordController;
use App\Http\Controllers\Auth\LockModeController;
use App\Http\Controllers\Company\DashboardController;
use App\Http\Controllers\company\InvitationController;
use App\Http\Controllers\Company\UserAccountController;
use App\Http\Controllers\Company\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\CheckForLockMode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Route::get('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitation.accept');
Route::post('/invitations/{token}/confirm', [InvitationController::class, 'acceptConfirm'])->name('invitation.confirm');
Route::middleware('auth', 'verified', 'mustBeActive', CheckForLockMode::class)->group(function () {

    Route::get('auth/lock', LockModeController::class.'@lock')->name('auth.lock');
    Route::post('auth/unlock', LockModeController::class.'@unlock')->name('auth.unlock');

    Route::prefix('password')->name('password.expired.')->group(function () {
        Route::view('expired', 'auth.expired-password');
        Route::post('expired', [ExpiredPasswordController::class, 'resetPassword'])->name('reset');
    });

    Route::middleware('passwordMustNotBeExpired')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('pages-home');
        Route::resource('user-account', UserAccountController::class);

    Route::get('users/roles/{role}', [UserController::class, 'showRole']);
    Route::resource('users', UserController::class);

    Route::post('/keep-alive', fn() => response()->json(['status' => __('success')]));

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('notification/count', [NotificationController::class, 'updateNotificationCount'])->name('notifications.count');
    Route::get('/pages/account-settings-notifications', [NotificationController::class, 'notificationTable'])->name('account.settings.notifications');


  });
});

require __DIR__.'/admin/admin.php';
