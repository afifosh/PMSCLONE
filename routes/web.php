<?php

use App\Http\Controllers\Auth\ExpiredPasswordController;
use App\Http\Controllers\Auth\LockModeController;
use App\Http\Controllers\Company\ApprovalRequestController;
use App\Http\Controllers\Company\CompanyProfile\AddressController;
use App\Http\Controllers\Company\CompanyProfile\BankAccountController;
use App\Http\Controllers\Company\CompanyProfile\ContactController;
use App\Http\Controllers\Company\CompanyProfile\DocumentController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\Company\DashboardController;

use App\Http\Controllers\Company\InvitationController;
use App\Http\Controllers\Company\UserAccountController;
use App\Http\Controllers\Company\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\CheckForLockMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use League\CommonMark\Node\Block\Document;


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

    Route::prefix('password')->middleware(['passwordMustBeExpired'])->name('password.expired.')->group(function () {
        Route::view('expired', 'auth.expired-password');
        Route::post('expired', [ExpiredPasswordController::class, 'resetPassword'])->name('reset');
    });

    Route::middleware('passwordMustNotBeExpired')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('pages-home');
      //  Route::resource('user/user-account', UserAccountController::class);
      Route::name('user.')->group(function(){
        Route::resource('user/user-account', UserAccountController::class)->only('edit');

      });
    Route::get('users/roles/{role}', [UserController::class, 'showRole']);
    Route::resource('users', UserController::class);
    Route::get('mt/o/{hash}', [MailTrackerController::class, 'opens'])->name('mail-tracker.open');
    Route::get('mt/l', [MailTrackerController::class, 'link'])->name('mail-tracker.link');


    Route::prefix('company-profile')->name('company.')->controller(CompanyProfileController::class)->group(function () {
      Route::get('/', 'editDetails')->name('editDetails');
      Route::get('/detailed-content', 'detailedContent')->name('profile.detailedContent');
      Route::get('/detailed-content/activity/{approval_request?}', 'showActivityTimeline')->name('profile.activityTimeline');
      Route::post('/', 'updateDetails')->name('updateDetails');
      Route::any('/submit-request', 'submitApprovalRequest')->name('submitApprovalRequest');
    });

    Route::prefix('company-profile')->name('company.')->group(function (){
      Route::resource('approval-requests', ApprovalRequestController::class);
      Route::resource('contacts', ContactController::class);
      Route::resource('addresses', AddressController::class);
      Route::post('kyc-documents/upload-doc', [DocumentController::class, 'uploadDocument'])->name('kyc-documents.upload-doc');
      Route::resource('kyc-documents', DocumentController::class);
      Route::resource('bank-accounts', BankAccountController::class);
    });

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('notification/count', [NotificationController::class, 'updateNotificationCount'])->name('notifications.count');
    Route::get('/pages/account-settings-notifications', [NotificationController::class, 'notificationTable'])->name('account.settings.notifications');


  });
});
Route::get('refresh-csrf', function(Request $request){
  // $request->session()->regenerateToken();
  return csrf_token();
})->name('refresh-csrf');
require __DIR__.'/admin/admin.php';
require __DIR__.'/core/app.php';
