<?php
use App\Http\Controllers\AppsController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\Company\ApprovalRequestController;
use App\Http\Controllers\Admin\Company\ContactPersonController;
use App\Http\Controllers\Admin\Company\InvitationController;
use App\Http\Controllers\Admin\Company\UserController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyRoleController;
use App\Http\Controllers\Admin\Partner\DepartmentController;
use App\Http\Controllers\Admin\Partner\DesignationController;
use App\Http\Controllers\Admin\Partner\PatnerCompanyController;
use App\Http\Controllers\Admin\Program\ProgramController;
use App\Http\Controllers\Admin\Program\ProgramUserController;
use App\Http\Controllers\Admin\RFP\FileShareController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\ExpiredPasswordController;
use App\Http\Controllers\Auth\LockModeController;
use App\Http\Middleware\CheckForLockMode;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RFP\RFPDraftController;
use App\Http\Controllers\Admin\RFP\RFPFileController;
use App\Http\Controllers\Admin\Setting\BroadcastSettingController;
use App\Http\Controllers\Admin\Setting\DeliverySettingController;
use App\Http\Controllers\Admin\Setting\GeneralSettingController;
use App\Http\Controllers\Admin\Setting\SecuritySettingController;
use App\Http\Controllers\Admin\SharedFileController;
use App\Http\Controllers\Admin\Workflow\WorkflowController;
use App\Http\Controllers\OnlyOfficeController;
use App\Models\RFPFile;
use App\Http\Controllers\Admin\EmailAccount\PersonalEmailAccountController;
use App\Http\Controllers\Admin\EmailAccount\EmailAccountSyncStateController;
use App\Http\Controllers\Admin\EmailAccount\EmailAccountPrimaryStateController;
use App\Http\Controllers\Admin\EmailAccount\EmailAccountConnectionTestController;
use App\Http\Controllers\Admin\OAuthController;
use App\Http\Controllers\Admin\OAuthEmailAccountController;

use App\Http\Controllers\Admin\EmailAccount\EmailAccountSync;
use App\Http\Controllers\Admin\EmailAccount\SharedEmailAccountController;
use App\Http\Controllers\Admin\EmailAccount\EmailAccountMessagesController;
use App\Http\Controllers\Admin\EmailAccount\EmailAccountController;

Route::prefix('admin')->name('admin.')->middleware('auth:admin', 'guest:web','adminVerified' , 'mustBeActive', CheckForLockMode::class)->group(function () {
  Route::get('/mail/accounts/{type}/{provider}/connect', [OAuthEmailAccountController::class, 'connect']);
  // Email accounts routes
  Route::prefix('mail/accounts')->group(function () {
   // Email accounts management
   Route::get('{account}/sync', EmailAccountSync::class);
   Route::put('{account}/update', [EmailAccountController::class, 'update']);
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
 
     
    //  Route::resource('emails', EmailAccountController::class);
     Route::get('/{providerName}/connect', [OAuthController::class, 'connect'])->where('providerName', 'microsoft|google');
     Route::get('/{providerName}/callback', [OAuthController::class, 'callback'])->where('providerName', 'microsoft|google');
     
     Route::prefix('emails')->group(function () {
      Route::post('{message}/read', [EmailAccountMessagesController::class, 'read']);
      Route::post('{message}/unread', [EmailAccountMessagesController::class, 'unread']);
      Route::delete('{message}', [EmailAccountMessagesController::class, 'destroy']);
      // reply method is used to check in MessageRequest
      Route::post('{message}/reply', [EmailAccountMessagesController::class, 'reply']);
      Route::post('{message}/forward', [EmailAccountMessagesController::class, 'forward']);
    });
    Route::get('/mail/accounts/manage-accounts', [EmailAccountController::class,'manageAccounts']);
    Route::resource('/mail/accounts', EmailAccountController::class);
 
    Route::prefix('inbox')->group(function () {
      Route::get('emails/folders/{folder_id}/{message}', [EmailAccountMessagesController::class, 'show']);
      Route::post('emails/{account_id}', [EmailAccountMessagesController::class, 'create']);
      Route::get('emails/{account_id}/{folder_id}', [EmailAccountMessagesController::class, 'index']);
    });   
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

    Route::post('/admin-account/update-profile-pic', [AdminAccountController::class, 'updateProfilePic'])->name('admin-account.update-profile');

    Route::resource('admin-account', AdminAccountController::class)->only('edit');
    Route::get('admin-account-auth-logs', [AdminAccountController::class, 'authLogs'])->name('auth-logs');

    Route::get('/pages/account-settings-notifications', [NotificationController::class, 'notificationTable'])->name('account.settings.notifications');

    Route::resource('roles', AdminRoleController::class);

    Route::resource('company-roles', CompanyRoleController::class);

    Route::get('/users/{user}/impersonate', [AdminUsersController::class, 'impersonate'])->name('impersonate-admin');
    Route::get('/users/leave-impersonate', [AdminUsersController::class, 'leaveImpersonate'])->name('leave-impersonate');
    Route::get('/users/{user}/editPassword', [AdminUsersController::class, 'editPassword'])->name('users.editPassword');
    Route::put('/users/{user}/updatePassword', [AdminUsersController::class, 'updatePassword'])->name('users.updatePassword');
    Route::resource('users', AdminUsersController::class);

    Route::get('companies/{company}/users', [CompanyController::class, 'showUsers'])->name('companies.showUsers');
    Route::get('companies/{company}/invitations', [CompanyController::class, 'showInvitations'])->name('companies.showInvitations');
    Route::resource('companies', CompanyController::class);
    Route::resource('companies.contact-persons', ContactPersonController::class);
    Route::match(['get', 'post'], 'company-invitations/{company_invitation}/revoke', [InvitationController::class, 'revokeInvitation'])->name('company-invitations.revoke');
    Route::get('/company-invitations/{company_invitation}/logs', [InvitationController::class, 'invitationLogs'])->name('company-invitations.logs');
    Route::resource('company-invitations', InvitationController::class);
    Route::resource('company-users', UserController::class);

    Route::prefix('partner')->name('partner.')->group(function () {
      Route::resource('companies', PatnerCompanyController::class);
      Route::get('departments/get-by-company', [DepartmentController::class, 'getByComapnyId'])->name('departments.getByCompany');
      Route::resource('departments', DepartmentController::class);
      Route::get('designations/get-by-department', [DesignationController::class, 'getByDepartmentId'])->name('designations.getByDepartment');
      Route::resource('designations', DesignationController::class);
    });


    Route::get('programs/{program}/draft-rfps', [ProgramController::class, 'showDraftRFPs'])->name('programs.showDraftRFPs');
    Route::resource('programs', ProgramController::class);
    Route::resource('programs.users', ProgramUserController::class);

    Route::get('draft-rfps/{draft_rfp}/users', [RFPDraftController::class, 'draft_users_tab'])->name('draft-rfps.users_tab');
    Route::get('draft-rfps/{draft_rfp}/activity', [RFPDraftController::class, 'draft_activity_tab'])->name('draft-rfps.activity_tab');
    Route::get('/draft-rfps/{draft_rfp}/files-activity', [RFPFileController::class, 'files_activity_tab'])->name('draft-rfps.files_activity');
    Route::get('/draft-rfps/create/{program_id?}', [RFPDraftController::class, 'create'])->name('draft-rfps.create');
    Route::resource('draft-rfps', RFPDraftController::class)->except('create');

    Route::get('/draft-rfps/{draft_rfp}/files/{file}/download', [RFPFileController::class, 'download'])->name('draft-rfps.files.download');
    Route::get('/draft-rfps/{draft_rfp}/files/{file}/restore', [RFPFileController::class, 'restoreFile'])->name('draft-rfps.files.restore');
    Route::get('/draft-rfps/{draft_rfp}/files/{file}/toggle-important', [RFPFileController::class, 'toggleImportant'])->name('draft-rfps.files.toggle-important');
    Route::get('/draft-rfps/{draft_rfp}/files/{file}/activity', [RFPFileController::class, 'getActivity'])->name('draft-rfps.files.get-activity');
    Route::delete('/draft-rfps/{draft_rfp}/files/{file}/move-to-trash', [RFPFileController::class, 'moveToTrash'])->name('draft-rfps.files.trash');
    Route::get('/draft-rfps/{draft_rfp}/files/{filter?}', [RFPFileController::class, 'index'])->whereIn('filter', RFPFile::ROUTE_FILTERS)->name('draft-rfps.files.index');
    Route::resource('draft-rfps.files', RFPFileController::class)->except(['index']);
    Route::match(['get', 'post'], '/draft-rfps/{draft_rfp}/files/{file}/shares/{share}/revoke', [FileShareController::class, 'revoke'])->name('draft-rfps.files.shares.revoke');
    Route::match(['get', 'post'], '/draft-rfps/{draft_rfp}/files/{file}/shares/{share}/reinvite', [FileShareController::class, 'reinvite'])->name('draft-rfps.files.shares.reinvite');
    Route::resource('draft-rfps.files.shares', FileShareController::class);
    Route::get('/edit-file/{file}/{rfp?}', [RFPFileController::class, 'editFileWithOffice'])->name('edit-file');
    Route::post('restore-file-update', [OnlyOfficeController::class, 'restoreVersion'])->name('file.restore_version');

    Route::get('/files/{file}/activity/{rfp?}', [SharedFileController::class, 'fileActivity'])->name('shared-files.file-activity');
    Route::get('/files/{file}/versions/{rfp?}', [SharedFileController::class, 'fileVersions'])->name('shared-files.file-versions');
    Route::resource('shared-files', SharedFileController::class)->only(['index']);

    Route::get('file-manager', [AppsController::class, 'file_manager'])->name('app-file-manager');

    Route::resource('workflows', WorkflowController::class)->only(['index', 'edit', 'update']);

    Route::get('approval-requests/level/{level}/companies/{company}', [ApprovalRequestController::class, 'getCompanyReqeust'])->name('approval-requests.level.companies.show');
    Route::post('approval-requests/level/{level}/companies/{company}', [ApprovalRequestController::class, 'updateApprovalRequest'])->name('approval-requests.level.companies.update');
    Route::resource('approval-requests', ApprovalRequestController::class)->only(['index', 'show', 'update']);

    // Route::resource('companies.invitations', InvitationController::class);
    Route::prefix('settings')->name('setting.')->group(function () {
      Route::prefix('security')->name('security.')->controller(SecuritySettingController::class)->group(function() {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
    });

      Route::prefix('general')->name('general.')->controller(GeneralSettingController::class)->group(function() {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
      });

      Route::prefix('delivery')->name('delivery.')->controller(DeliverySettingController::class)->group(function() {
        Route::get('', 'index')->name('index');
        Route::get('show', 'show')->name('show');
        Route::put('', 'update')->name('update');
      });

      Route::prefix('broadcast')->name('broadcast.')->controller(BroadcastSettingController::class)->group(function() {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
      });
    });


    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('notifications/count', [NotificationController::class, 'updateNotificationCount'])->name('notifications.count');
  });
});

Route::any('update-file/{file}', [OnlyOfficeController::class, 'updateFile'])->name('update-file');

require __DIR__.'/auth.php';
