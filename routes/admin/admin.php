<?php

use App\Http\Controllers\AppsController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\Client\ClientController;
use App\Http\Controllers\Admin\Company\ApprovalRequestController;
use App\Http\Controllers\Admin\Company\ContactPersonController;
use App\Http\Controllers\Admin\Company\InvitationController;
use App\Http\Controllers\Admin\Company\KycDocumentController;
use App\Http\Controllers\Admin\Company\UserController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyRoleController;
use App\Http\Controllers\Admin\Contract\BulkInvoiceController;
use App\Http\Controllers\Admin\Contract\ChangeRequestController;
use App\Http\Controllers\Admin\Contract\ContractCategoryController;
use App\Http\Controllers\Admin\Contract\ContractController;
use App\Http\Controllers\Admin\Contract\ContractDocumentController;
use App\Http\Controllers\Admin\Contract\ContractPhaseController;
use App\Http\Controllers\Admin\Contract\ContractSettingController;
use App\Http\Controllers\Admin\Contract\ContractTermController;
use App\Http\Controllers\Admin\Contract\ContractTypeController;
use App\Http\Controllers\Admin\Contract\EventController;
use App\Http\Controllers\Admin\Contract\NotifiableUserController;
use App\Http\Controllers\Admin\Contract\PaymentScheduleController;
use App\Http\Controllers\Admin\EmailTemplate\EmailTemplateController;
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
use App\Http\Controllers\Admin\Setting\SecuritySettingController;
use App\Http\Controllers\Admin\Setting\OnlyOfficeSettingController;
use App\Http\Controllers\Admin\SharedFileController;
use App\Http\Controllers\Admin\Workflow\WorkflowController;
use App\Http\Controllers\OnlyOfficeController;
use App\Models\RFPFile;
use App\Http\Controllers\Admin\MailClient\EmailAccountController;
use App\Http\Controllers\Admin\MediaViewController;
use App\Http\Controllers\Admin\PersonalNote\PersonalNoteController;
use App\Http\Controllers\Admin\Contract\ContractStageController;
use App\Http\Controllers\Admin\Contract\DocumentStatController;
use App\Http\Controllers\Admin\Contract\LogController;
use App\Http\Controllers\Admin\Contract\UploadedDocumentController;
use App\Http\Controllers\Admin\ContractDoc\DocControlController;
use App\Http\Controllers\Admin\DocControl\InvoiceDocController;
use App\Http\Controllers\Admin\Finance\ProgramTransactionController;
use App\Http\Controllers\Admin\Finance\FinancialYearController;
use App\Http\Controllers\Admin\Finance\FinancialYearTransactionController;
use App\Http\Controllers\Admin\Finance\PaymentController;
use App\Http\Controllers\Admin\Finance\ProgramAccountController;
use App\Http\Controllers\Admin\Finance\TaxController;
use App\Http\Controllers\Admin\Invoice\AttachmentController;
use App\Http\Controllers\Admin\Invoice\DownpaymentController;
use App\Http\Controllers\Admin\Invoice\InvoiceController;
use App\Http\Controllers\Admin\Invoice\InvoiceItemController;
use App\Http\Controllers\Admin\Invoice\InvoiceTaxController;
use App\Http\Controllers\Admin\Invoice\MergeInvoiceController;
use App\Http\Controllers\Admin\Invoice\StatisticController;
use App\Http\Controllers\Admin\Project\GanttChartController;
use App\Http\Controllers\Admin\Project\ImportTemplateController;
use App\Http\Controllers\Admin\Project\ProjectCategoryController;
use App\Http\Controllers\Admin\Project\ProjectController;
use App\Http\Controllers\Admin\Project\ProjectPhaseController;
use App\Http\Controllers\Admin\Project\ProjectTaskController;
use App\Http\Controllers\Admin\Project\ProjectTemplateController;
use App\Http\Controllers\Admin\Project\TaskBoardController;
use App\Http\Controllers\Admin\Project\TaskChecklistController;
use App\Http\Controllers\Admin\Project\TaskFileController;
use App\Http\Controllers\Admin\Project\TaskReminderController;
use App\Http\Controllers\Admin\Project\TemplateTaskCheckItemController;
use App\Http\Controllers\Admin\Project\TemplateTaskController;
use App\Http\Controllers\Admin\ResourceSearchController;
use App\Http\Controllers\Admin\Setting\ContractSettingController as SettingContractSettingController;
use App\Http\Controllers\Admin\Setting\OauthGoogleController;
use App\Http\Controllers\Admin\Setting\OauthMicrosoftController;
use Modules\Core\Http\Controllers\OAuthController;
use Modules\MailClient\Http\Controllers\OAuthEmailAccountController;

use App\Http\Controllers\Admin\ArtworkController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\MediumController;
use App\Http\Controllers\Admin\ArtistController;
use App\Http\Controllers\Admin\Contract\ContractPartyController;
use App\Http\Controllers\Admin\Contract\UploadedDoc\SignatureController;
use App\Http\Controllers\Admin\Invoice\InvoiceStatusController;
use App\Http\Controllers\Admin\Invoice\ItemDeductionController;
use App\Http\Controllers\Admin\Invoice\ItemTaxController;
use App\Http\Controllers\Admin\Invoice\TaxAuthorityInvoiceController;

Route::view('/inbox', 'admin.pages.email.index')->name('email')->middleware('auth:admin');
Route::prefix('admin')->name('admin.')->middleware('auth:admin', 'guest:web', 'adminVerified', 'mustBeActive', CheckForLockMode::class)->group(function () {
  Route::get('/mail/accounts/{type}/{provider}/connect', [OAuthEmailAccountController::class, 'connect']);
  // Email accounts routes
  Route::prefix('mail/accounts')->group(function () {
    Route::get('{account}/share', [EmailAccountController::class, 'share']);
    Route::post('{account}/setPermission', [EmailAccountController::class, 'setPermission']);
  });
  Route::get('/{providerName}/connect', [OAuthController::class, 'connect'])->where('providerName', 'microsoft|google');
  Route::get('/{providerName}/callback', [OAuthController::class, 'callback'])->where('providerName', 'microsoft|google');

  Route::get('/mail/accounts/manage-accounts', [EmailAccountController::class, 'index'])->name('mail.accounts.manage');
  Route::resource('/mail/accounts', EmailAccountController::class);

  Route::post('/keep-alive', fn () => response()->json(['status' => __('success')]))->name('alive');
  Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('lock', [LockModeController::class, 'lock'])->name('lock');
    Route::post('unlock', [LockModeController::class, 'unlock'])->name('unlock');
  });

  Route::prefix('password')->middleware(['passwordMustBeExpired'])->name('password.expired.')->group(function () {
    Route::view('expired', 'admin.auth.expired-password');
    Route::post('expired', [ExpiredPasswordController::class, 'resetPassword'])->name('reset');
  });

  Route::middleware('passwordMustNotBeExpired')->group(function () {
    Route::get('/', [GanttChartController::class, 'index'])->name('dashboard');

    Route::resource('private-notes', PersonalNoteController::class);

    Route::post('/admin-account/update-profile-pic', [AdminAccountController::class, 'updateProfilePic'])->name('admin-account.update-profile');
    Route::put('/admin-account/email', [AdminAccountController::class, 'updateEmail'])->name('account.update-email');
    Route::delete('/admin-account/email', [AdminAccountController::class, 'removePendingMail'])->name('account.update-email.destroy');
    Route::post('/admin-account/email/resend', [AdminAccountController::class, 'resendVerificationEmail'])->name('account.resend-verification-email');

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
    // Route::get('companies/{company}/users', [CompanyController::class, 'showUsers'])->name('companies.showUsers');
    Route::get('companies/{company}/invitations', [CompanyController::class, 'showInvitations'])->name('companies.showInvitations');
    Route::get('companies/{company}/invoices', [InvoiceController::class, 'index'])->name('companies.invoices.index');
    Route::get('companies/{company}/payments', [PaymentController::class, 'index'])->name('companies.payments.index');
    Route::resource('companies', CompanyController::class);
    Route::resource('companies.contracts', ContractController::class);
    Route::resource('companies.contacts', UserController::class);
    Route::resource('companies.contact-persons', ContactPersonController::class);
    Route::match(['get', 'post'], 'company-invitations/{company_invitation}/revoke', [InvitationController::class, 'revokeInvitation'])->name('company-invitations.revoke');
    Route::get('/company-invitations/{company_invitation}/logs', [InvitationController::class, 'invitationLogs'])->name('company-invitations.logs');
    Route::resource('company-invitations', InvitationController::class);
    // Route::resource('company-users', UserController::class);

    // Artist routes
    Route::resource('artists', ArtistController::class);
    // Artwork routes
    Route::resource('artworks', ArtworkController::class);
    // Location routes
    Route::resource('locations', LocationController::class);
    // Warehouse routes
    Route::resource('warehouses', WarehouseController::class);
    // Studio routes
    Route::resource('studios', StudioController::class);
    // Medium routes
    Route::resource('mediums', MediumController::class);


    Route::prefix('partner')->name('partner.')->group(function () {
      Route::resource('companies', PatnerCompanyController::class);
      Route::get('departments/get-by-company', [DepartmentController::class, 'getByComapnyId'])->name('departments.getByCompany');
      Route::resource('departments', DepartmentController::class);
      Route::get('designations/get-by-department', [DesignationController::class, 'getByDepartmentId'])->name('designations.getByDepartment');
      Route::resource('designations', DesignationController::class);
    });


    Route::get('projects/gantt-chart', [GanttChartController::class, 'index'])->name('projects.gantt-chart.index');
    Route::get('programs/{program}/draft-rfps', [ProgramController::class, 'showDraftRFPs'])->name('programs.showDraftRFPs');
    // Route for listing invoices for a program
    Route::get('programs/{program}/invoices', [ProgramController::class, 'invoices'])->name('programs.invoices');
    // Route for listing contracts for a program
    //Route::get('programs/{program}/contracts', [ProgramController::class, 'contracts'])->name('programs.contracts');


    Route::put('projects/{project}/contracts/{contract}/sort-phases', [ProjectPhaseController::class, 'sortPhases'])->name('projects.contracts.sort-phases');
    Route::resource('programs', ProgramController::class);
    Route::resource('programs.users', ProgramUserController::class);
    Route::resource('programs.contracts', ContractController::class);
    Route::resource('programs.invoices', InvoiceController::class);
    Route::resource('programs.payments', PaymentController::class);
    Route::resource('doc-signatures', SignatureController::class);

    // Route::prefix('contracts')->group(function () {
    //   Route::get('paymentsplan',  [ContractController::class, 'ContractPaymentsPlan'])->name('contracts.paymentsplan');
    //   // You can add more routes specific to contracts here
    //   Route::get('document-stats', [DocumentStatController::class, 'index'])->name('contracts.document-stats.index');
    //   Route::get('change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');

    //   // Add the new route for paymentsplans with a specific contract id
    //   Route::get('paymentsplan/{contract_id}/phases', [ContractController::class, 'ContractPaymentsPlanPhases'])->name('contracts.paymentsplan.phases');
    //   Route::get('paymentsplan/{contract_id}/stages', [ContractController::class, 'ContractPaymentsPlanStages'])->name('contracts.paymentsplan.stages');
    //   //Route::get('{contract_id}/paymentsplan', [ContractController::class, 'SpecificContractPaymentsPlan'])->name('contracts.specific.paymentsplan');

    //   // Route to mark a phase as complete
    //   Route::post('paymentsplan/{contract_id}/phases/{phase_id}/mark-complete', [ContractController::class, 'markPhaseAsComplete'])->name('contracts.phases.complete');
    //   // Route to mark a phase as incomplete
    //   Route::post('paymentsplan/{contract_id}/phases/{phase_id}/mark-incomplete', [ContractController::class, 'markPhaseAsIncomplete'])->name('contracts.phases.incomplete');


    // });

  //   Route::prefix('contracts')->group(function () {
  //     // Your existing general contract routes here
  //     Route::get('paymentsplan', [ContractController::class, 'ContractPaymentsPlan'])->name('contracts.paymentsplan');
  //     Route::get('document-stats', [DocumentStatController::class, 'index'])->name('contracts.document-stats.index');
  //     Route::get('change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');

  //     // Grouped routes for payment plans with contract_id constraint
  //     Route::prefix('paymentsplan/{contract_id}')->where(['contract_id' => '[0-9]+'])->group(function () {
  //         // Route for a contract's payment plan details
  //         Route::get('/', [ContractController::class, 'SpecificContractPaymentsPlan'])->name('contracts.specific.paymentsplan');

  //         // Route for phases of a specific contract's payment plan
  //         Route::get('/phases', [ContractController::class, 'ContractPaymentsPlanPhases'])->name('contracts.paymentsplan.phases');

  //         // Route for stages of a specific contract's payment plan
  //         Route::get('/stages', [ContractController::class, 'ContractPaymentsPlanStages'])->name('contracts.paymentsplan.stages');

  //         // Phase actions
  //         Route::post('/phases/{phase_id}/mark-complete', [ContractController::class, 'markPhaseAsComplete'])->name('contracts.phases.complete')->where('phase_id', '[0-9]+');
  //         Route::post('/phases/{phase_id}/mark-incomplete', [ContractController::class, 'markPhaseAsIncomplete'])->name('contracts.phases.incomplete')->where('phase_id', '[0-9]+');

  //         // Specific phase tab routes with phase_id constraint
  //         Route::prefix('/phase/{phase_id}')->where(['phase_id' => '[0-9]+'])->group(function () {
  //             Route::get('/reviewers', [PhaseController::class, 'reviewersTabContent'])->name('phases.reviewers');
  //             Route::get('/comments', [PhaseController::class, 'commentsTabContent'])->name('phases.comments');
  //             Route::get('/activity', [PhaseController::class, 'activityTabContent'])->name('phases.activity');
  //             // ... add more routes for each tab as needed
  //         });
  //     });
  // });


// Prefix for all contract-related routes
Route::prefix('contracts')->group(function () {
  Route::get('/afif', [ContractController::class, 'getContractsWithStagesAndPhases']);
  // General contract routes
  Route::get('document-stats', [DocumentStatController::class, 'index'])->name('contracts.document-stats.index');
  Route::get('change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');
  Route::get('paymentsplan', [ContractController::class, 'ContractPaymentsPlan'])->name('contracts.paymentsplan');

  Route::prefix('tracking')->group(function () {
    // Route for reviewing contracts
    Route::get('contracts', [ContractController::class, 'trackingContract'])->name('tracking.contracts');

    // Route for reviewing payment plans
    Route::get('paymentsplan', [ContractController::class, 'trackingPaymentsPlan'])->name('tracking.paymentsplan');
  });

  // Specific contract routes with contract_id constraint
  Route::prefix('{contract}')->where(['contract' => '[0-9]+'])->group(function () {


    Route::get('/stages-status-datatable', function (App\Models\Contract $contract, DataTables $dataTables) {

      // Assuming you have a method in your Contract model that returns the necessary collection
      $data = $contract->getAllUsersStagesReviewStatusWithlastReviewDate();
  dd($data);
      // Convert the collection to a Laravel Collection if it's not already
      $collection = collect($data);

      // Use the DataTables API to handle server-side operations like pagination, search, etc.
      return $dataTables->collection($collection)
          ->make(true);
  })->name('contract.stages-status');

        // Display the form for editing contract details
        // Process the action of marking a contract as reviewed
        Route::post('/toggle-review', [ContractController::class, 'toggleContractReviewStatus'])->name('contracts.toggle-review');
        // Dynamic content loading for each contract tab
        Route::get('/summary', [ContractController::class, 'showSummary'])->name('contracts.summary');
        Route::get('/reviewers', [ContractController::class, 'showReviewers'])->name('contracts.reviewers');
        Route::get('/comments', [ContractController::class, 'showComments'])->name('contracts.comments');
        Route::get('/activities', [ContractController::class, 'showActivities'])->name('contracts.activities');

      // Details of a specific contract
      // Route::get('/', [ContractController::class, 'show'])->name('contracts.show');

      // Payment plan and its sub-resources for a specific contract
      Route::group(['prefix' => 'paymentsplan'], function () {

          // Route for a contract's payment plan details
          Route::get('/', [ContractController::class, 'SpecificContractPaymentsPlan'])->name('contracts.specific.paymentsplan');

          // Route for the phases of a contract's payment plan
          Route::get('/phases', [ContractController::class, 'ContractPaymentsPlanPhases'])->name('contracts.paymentsplan.phases');

          // Route for the stages of a contract's payment plan
          Route::get('/stages', [ContractController::class, 'ContractPaymentsPlanStages'])->name('contracts.paymentsplan.stages');

          // Route for the review of a contract's payment plan
          Route::get('/review', [ContractController::class, 'ContractPaymentsPlanReview'])->name('contracts.paymentsplan.review');
      });

      // Routes for actions on phases
      Route::prefix('phases/{phase_id}')->where(['phase_id' => '[0-9]+'])->group(function () {
          // Route::post('/mark-complete', [ContractController::class, 'markPhaseAsComplete'])->name('contracts.phases.mark-complete');
          // Route::post('/mark-incomplete', [ContractController::class, 'markPhaseAsIncomplete'])->name('contracts.phases.mark-incomplete');
          // Single route to toggle the review status of a phase
          Route::post('/toggle-review', [ContractController::class, 'togglePhaseReviewStatus'])->name('contracts.phases.toggle-review');

          // Dynamic content loading for each phase tab
          // Route::get('/reviewers', [PhaseController::class, 'reviewersTabContent'])->name('phases.reviewers');
          // Route::get('/comments', [PhaseController::class, 'commentsTabContent'])->name('phases.comments');
          // Route::get('/activity', [PhaseController::class, 'activityTabContent'])->name('phases.activity');
          // ... additional routes for each tab as needed
      });
  });
  // ... other non-contract-specific routes
});



    Route::get('contracts/{contract}/stages/{stage}/phases', [ProjectPhaseController::class, 'contractPhases'])->name('contracts.stages.phases.index');
    Route::get('contracts/statistics', [ContractController::class, 'statistics'])->name('contracts.statistics');
    // Route::get('contracts/change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');
    Route::get('contracts/{contract}/invoices', [InvoiceController::class, 'index'])->name('contracts.invoices.index');
    Route::get('contracts/{contract}/payments', [PaymentController::class, 'index'])->name('contracts.payments.index');
    Route::post('contracts/{contract}/release-retentions', [ContractController::class, 'releaseRetention'])->name('contracts.release-retentions');
    Route::resource('contracts', ContractController::class);
    Route::resource('contracts.contract-parties', ContractPartyController::class);
    Route::resource('contracts.logs', LogController::class)->only(['index']);
    Route::resource('contracts.change-requests', ChangeRequestController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('contracts/{contract}/change-requests/{change_request}/approve', [ChangeRequestController::class, 'approve'])->name('contracts.change-requests.approve');
    Route::post('contracts/{contract}/change-requests/{change_request}/reject', [ChangeRequestController::class, 'reject'])->name('contracts.change-requests.reject');
    Route::resource('contracts.settings', ContractSettingController::class)->only(['index']);
    Route::resource('contracts.notifiable-users', NotifiableUserController::class)->only(['create', 'store', 'destroy']);
    Route::resource('contracts.events', EventController::class)->only(['index']);
    Route::put('contracts/{contract}/terminate', [ContractSettingController::class, 'terminate'])->name('contracts.terminate');
    Route::put('contracts/{contract}/undo-terminate', [ContractSettingController::class, 'undoTerminate'])->name('contracts.undo-terminate');
    Route::put('contracts/{contract}/pause', [ContractSettingController::class, 'pause'])->name('contracts.pause');
    Route::put('contracts/{contract}/resume', [ContractSettingController::class, 'resume'])->name('contracts.resume');
    Route::post('contracts/{contract}/upload-requested-doc', [ContractDocumentController::class, 'uploadDocument'])->name('contracts.upload-requested-doc');
    Route::resource('contracts.payment-schedules', PaymentScheduleController::class);
    // Route::resource('contracts.stages', ContractStageController::class);
    Route::resource('contract-types', ContractTypeController::class);
    Route::resource('contract-categories', ContractCategoryController::class);

    Route::get('projects/{project}/contracts', [ContractController::class, 'projectContractsIndex'])->name('projects.contracts.index');
    Route::resource('contracts.stages', ContractStageController::class);
    Route::resource('contracts.pending-documents', ContractDocumentController::class)->only(['index', 'store']);
    Route::resource('contracts.uploaded-documents', UploadedDocumentController::class);
    Route::resource('contracts.bulk-invoices', BulkInvoiceController::class)->only(['store']);
    Route::resource('projects.contracts.stages.phases', ProjectPhaseController::class);
    Route::resource('contract-doc-controls', DocControlController::class);
    Route::get('projects/get-company-by-project', [ProjectController::class, 'getCompanyByProject'])->name('projects.getCompanyByProject');
    Route::get('projects/{project}/gantt-chart', [ProjectController::class, 'ganttChart'])->name('projects.gantt-chart');
    Route::resource('projects', ProjectController::class);
    Route::put('project-templates/{project_template}/order-check-item', [ProjectTemplateController::class, 'orderCheckItem'])->name('project-templates.order-check-item');
    Route::resource('project-templates', ProjectTemplateController::class);
    Route::resource('project-templates.tasks', TemplateTaskController::class);
    Route::resource('project-templates.tasks.check-items', TemplateTaskCheckItemController::class);
    Route::put('/projects/{project}/tasks/update-order', [ProjectTaskController::class, 'updateOrder'])->name('projects.tasks.update-order');
    Route::resource('projects.import-templates', ImportTemplateController::class);
    Route::resource('projects.tasks', ProjectTaskController::class);
    Route::put('projects/{project}/tasks/{task}/hide-completed/{status?}', [ProjectTaskController::class, 'hideCompleted'])->name('projects.tasks.hide-completed');
    Route::resource('projects.tasks.files', TaskFileController::class)->only(['index', 'store', 'destroy']);
    Route::resource('projects.tasks.reminders', TaskReminderController::class)->only(['index', 'store', 'destroy']);
    Route::put('projects/{project}/tasks/{task}/checklist-items/update-order', [TaskChecklistController::class, 'updateOrder'])->name('projects.tasks.checklist-items.update-order');
    Route::get('projects/{project}/tasks/{task}/checklist-items/{checklist_item}/restore', [TaskChecklistController::class, 'restore'])->name('projects.tasks.checklist-items.restore');
    Route::put('projects/{project}/tasks/{task}/checklist-items/{checklist_item}/update-status', [TaskChecklistController::class, 'update_status'])->name('projects.tasks.checklist-items.update-status');
    Route::resource('projects.tasks.checklist-items', TaskChecklistController::class);
    Route::resource('project-categories', ProjectCategoryController::class);
    Route::put('projects/{project}/sort-board-tasks', [TaskBoardController::class, 'sortBoardTasks'])->name('projects.sort-board-tasks');
    Route::resource('projects.board-tasks', TaskBoardController::class);

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
    Route::resource('kyc-documents', KycDocumentController::class);

    Route::get('approval-requests/level/{level}/companies/{company}/{tab?}', [ApprovalRequestController::class, 'getCompanyReqeust'])
      ->whereIn('tab', ['details', 'contact-persons', 'addresses', 'documents', 'bank-accounts'])->name('approval-requests.level.companies.show');
    Route::post('approval-requests/level/{level}/companies/{company}', [ApprovalRequestController::class, 'updateApprovalRequest'])->name('approval-requests.level.companies.update');

    Route::get('pending-companies', [ApprovalRequestController::class, 'index'])->name('pending-companies.index');
    Route::get('verified-companies', [ApprovalRequestController::class, 'index'])->name('verified-companies.index');
    Route::get('change-requests', [ApprovalRequestController::class, 'index'])->name('change-requests.index');
    Route::get('approval-requests/history', [ApprovalRequestController::class, 'indexHistory'])->name('approval-requests.indexHistory');
    Route::resource('approval-requests', ApprovalRequestController::class)->only(['index', 'show', 'update']);

    // Route::resource('companies.invitations', InvitationController::class);
    Route::prefix('settings')->name('setting.')->group(function () {
      Route::prefix('security')->name('security.')->controller(SecuritySettingController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
      });

      Route::prefix('broadcast')->name('broadcast.')->controller(BroadcastSettingController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
      });

      Route::prefix('onlyoffice')->name('onlyoffice.')->controller(OnlyOfficeSettingController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
      });

      Route::prefix('contract-notifications')->name('contract-notifications.')->controller(SettingContractSettingController::class)->group(function () {
        Route::get('', 'create')->name('create');
        Route::put('', 'update')->name('update');
      });

      Route::resource('oauth-google', OauthGoogleController::class)->only(['create', 'store']);
      Route::resource('oauth-microsoft', OauthMicrosoftController::class)->only(['create', 'store']);
    });


    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::put('notifications/count', [NotificationController::class, 'updateNotificationCount'])->name('notifications.count');
    Route::get('email-templates/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->name('manage.email.language');
    Route::resource('email_template', EmailTemplateController::class)->only(['update']);

    // Finance Routes
    Route::prefix('finances')->name('finances.')->group(function(){
      Route::resource('financial-years', FinancialYearController::class);
      Route::resource('financial-years.transactions', FinancialYearTransactionController::class)->only(['index', 'create', 'store', 'show']);
      Route::resource('program-accounts', ProgramAccountController::class);
      Route::resource('program-accounts.transactions', ProgramTransactionController::class)->only(['index', 'create', 'store']);
      Route::get('retentions', [TaxController::class, 'retentions'])->name('retentions.index');
      Route::get('downpayments', [TaxController::class, 'downpayments'])->name('downpayments.index');
      Route::resource('taxes', TaxController::class);
      Route::resource('payments', PaymentController::class);
    });

    // Invoice Routes
    // Route::prefix('invoices')->name('invoices.')->group(function(){

    // });
    Route::get('invoices/document-stats', [DocumentStatController::class, 'index'])->name('invoices.document-stats.index');
    Route::get('invoices/{invoice}/payments', [PaymentController::class, 'index'])->name('invoices.payments.index');
    Route::get('invoices-statistics', StatisticController::class)->name('invoices.statistics');
    Route::put('invoices/{invoice}/invoice-tems/sort', [InvoiceController::class, 'sortItems'])->name('invoices.invoice-items.sort');
    Route::post('invoices/{invoice}/release-retention', [InvoiceController::class, 'releaseRetention'])->name('invoices.release-retention');
    Route::post('invoices/{invoice}/upload-requested-doc', [ContractDocumentController::class, 'uploadDocument'])->name('invoices.upload-requested-doc');
    Route::resource('invoices', InvoiceController::class);
    Route::resource('tax-authority-invoices', TaxAuthorityInvoiceController::class)->only(['index', 'show', 'destroy']);
    Route::post('invoices/{invoice}/invoice-items/store-bulk', [InvoiceItemController::class, 'storeBulk'])->name('invoices.invoice-items.store-bulk');
    Route::resource('invoices.status', InvoiceStatusController::class)->only(['create', 'store']);
    Route::resource('invoices.invoice-items', InvoiceItemController::class)->only(['index', 'create','store', 'edit', 'update', 'destroy']);
    Route::resource('invoices.invoice-items.taxes', ItemTaxController::class)->except(['index', 'show']);
    Route::resource('invoices.invoice-items.deductions', ItemDeductionController::class)->except(['index', 'show']);
    Route::resource('invoices.tax-rates', InvoiceTaxController::class);
    Route::resource('invoices.downpayments', DownpaymentController::class)->only(['store']);
    Route::resource('invoices.attachments', AttachmentController::class)->only('store', 'destroy');
    Route::resource('invoices.merge-invoices', MergeInvoiceController::class)->only(['create', 'store']);
    Route::resource('invoice-doc-controls', DocControlController::class);
    Route::resource('invoices.pending-documents', ContractDocumentController::class)->only(['index', 'store']);
    Route::resource('invoices.uploaded-documents', UploadedDocumentController::class);
  });
});
Route::get('/media/{token}/download', [MediaViewController::class, 'download']);
Route::get('/resource-select/{resource}', [ResourceSearchController::class, 'index'])->name('resource-select');
Route::get('/resource-select-user/{resource}', [ResourceSearchController::class, 'userSelect'])->name('resource-select-user');

Route::any('update-file/{file}', [OnlyOfficeController::class, 'updateFile'])->name('update-file');

require __DIR__ . '/auth.php';
