<?php

use App\Http\Controllers\Auth\ExpiredPasswordController;
use App\Http\Controllers\Auth\LockModeController;
use App\Http\Controllers\Company\CompanyProfile\AddressController;
use App\Http\Controllers\Company\CompanyProfile\BankAccountController;
use App\Http\Controllers\Company\CompanyProfile\ContactController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\Company\DashboardController;
use App\Http\Controllers\Company\EmailAccount\EmailAccountController;
use App\Http\Controllers\company\InvitationController;
use App\Http\Controllers\Company\UserAccountController;
use App\Http\Controllers\Company\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\CheckForLockMode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Company\EmailAccount\EmailAccountSync;
use App\Http\Controllers\Company\EmailAccount\SharedEmailAccountController;
use App\Http\Controllers\Company\EmailAccount\EmailAccountMessagesController;
use App\Http\Controllers\Company\EmailAccount\PersonalEmailAccountController;
use App\Http\Controllers\Company\EmailAccount\EmailAccountSyncStateController;
use App\Http\Controllers\Company\EmailAccount\EmailAccountPrimaryStateController;
use App\Http\Controllers\Company\EmailAccount\EmailAccountConnectionTestController;
use App\Http\Controllers\Company\OAuthEmailAccountController;
use App\Http\Controllers\MailTrackerController;

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
    Route::get('mt/o/{hash}', [MailTrackerController::class, 'opens'])->name('mail-tracker.open');
Route::get('mt/l', [MailTrackerController::class, 'link'])->name('mail-tracker.link');

    Route::get('/mail/accounts/{type}/{provider}/connect', [OAuthEmailAccountController::class, 'connect']);
 // Email accounts routes
 Route::prefix('mail/accounts')->group(function () {
  // Email accounts management
  Route::get('{account}/sync', EmailAccountSync::class);
  Route::get('unread', [EmailAccountController::class, 'unread']);

  // The GET route for all shared accounts
  Route::get('shared', SharedEmailAccountController::class)->middleware('permission:access shared inbox');

  // The GET route for all logged in user personal mail accounts
  Route::get('personal', PersonalEmailAccountController::class);

  // Test connection route
  Route::post('connection', [EmailAccountConnectionTestController::class, 'handle']);

  Route::put('{account}/primary', [EmailAccountPrimaryStateController::class, 'update']);
  Route::delete('primary', [EmailAccountPrimaryStateController::class, 'destroy']);
  Route::post('{account}/sync/enable', [EmailAccountSyncStateController::class, 'enable']);
  Route::post('{account}/sync/disable', [EmailAccountSyncStateController::class, 'disable']);
});

Route::resource('/mail/accounts', EmailAccountController::class);

    Route::resource('emails', EmailAccountController::class);


    Route::prefix('company-profile')->name('company.')->controller(CompanyProfileController::class)->group(function () {
      Route::get('/', 'editDetails')->name('editDetails');
      Route::post('/', 'updateDetails')->name('updateDetails');
      Route::any('/submit-request', 'submitApprovalRequest')->name('submitApprovalRequest');
    });

    Route::prefix('company-profile')->name('company.')->group(function (){
      Route::resource('contacts', ContactController::class);
      Route::resource('addresses', AddressController::class);
      Route::resource('bank-accounts', BankAccountController::class);
    });

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('notification/count', [NotificationController::class, 'updateNotificationCount'])->name('notifications.count');
    Route::get('/pages/account-settings-notifications', [NotificationController::class, 'notificationTable'])->name('account.settings.notifications');


  });
});
Route::prefix('emails')->group(function () {
  Route::post('{message}/read', [EmailAccountMessagesController::class, 'read']);
  Route::post('{message}/unread', [EmailAccountMessagesController::class, 'unread']);
  Route::delete('{message}', [EmailAccountMessagesController::class, 'destroy']);
  // reply method is used to check in MessageRequest
  Route::post('{message}/reply', [EmailAccountMessagesController::class, 'reply']);
  Route::post('{message}/forward', [EmailAccountMessagesController::class, 'forward']);
});

Route::prefix('inbox')->group(function () {
  Route::get('emails/folders/{folder_id}/{message}', [EmailAccountMessagesController::class, 'show']);
  Route::post('emails/{account_id}', [EmailAccountMessagesController::class, 'create']);
  Route::get('emails/{account_id}/{folder_id}', [EmailAccountMessagesController::class, 'index']);
});
require __DIR__.'/admin/admin.php';
