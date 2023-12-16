<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ChangeRequestsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\ChangeRequestStoreRequest;
use App\Models\Contract;
use App\Models\ContractChangeRequest;
use App\Support\LaravelBalance\Dto\TransactionDto;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Traits\FinanceTrait;

class ChangeRequestController extends Controller
{
  use FinanceTrait;

  public function __construct()
  {
    // check if change request belongs to contract
    $this->middleware('verifyContractNotTempered:changeRequest,contract_id')->only(['destroy', 'approve', 'reject']);

    $this->middleware('permission:read contract')->only(['index']);
    $this->middleware('permission:update contract')->only(['create', 'store', 'approve', 'reject', 'pauseContract', 'resumeContract', 'terminateContract', 'update']);
    $this->middleware('permission:delete contract')->only(['destroy']);
  }

  public function index(Contract $contract, ChangeRequestsDataTable $dataTable)
  {
    $data['contract'] = $contract;
    if ($contract)
      $dataTable->contract = $contract;

    if (!$contract->id) {
      $data['contracts'] = Contract::has('changeRequests')->pluck('subject', 'id')->prepend('All', '0');
    }

    return $dataTable->render('admin.pages.contracts.change-requests.index', $data);
    // view('admin.pages.contracts.change-requests.index')
  }

  public function create($contract)
  {
    $contract = Contract::where('id', $contract)->firstOrNew();
    $change_order = new ContractChangeRequest();
    $currency = config('money.currencies.' . ($contract->currency ?? config('money.defaults.currency')));

    $currency = [$contract->currency ?? config('money.defaults.currency') => $currency['name']];

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.change-requests.create', compact('contract', 'change_order', 'currency'))->render()]);
  }

  public function store(ChangeRequestStoreRequest $request, Contract $contract)
  {
    if ($request->action_type == 'pause-contract') {
      return $this->pauseContract($request, $contract);
    } else if ($request->action_type == 'terminate-contract') {
      return $this->terminateContract($request, $contract);
    } else if ($request->action_type == 'resume-contract') {
      return $this->resumeContract($request, $contract);
    }

    if ($request->value_action != 'unchanged') {
      $new_value = (int) $contract->value + ($request->value_action == 'inc' ? $request->value_change : -$request->value_change);
    } else {
      $new_value = (int) $contract->value;
    }

    $contract->changeRequests()->create([
      'sender_type' => 'App\Models\Admin',
      'sender_id' => auth()->id(),
      'visible_to_client' => $request->boolean('visible_to_client'),
      'reason' => $request->reason,
      'description' => $request->description,
      'old_value' => $contract->value,
      'new_value' => $new_value,
      'old_currency' => $contract->currency,
      'new_currency' => $request->value_action != 'unchanged' ? $request->currency : $contract->currency,
      'old_end_date' => $contract->end_date,
      'new_end_date' => $request->timeline_action != 'unchanged' ? $request->new_end_date : $contract->end_date,
    ]);

    return $this->sendRes('Change Request Added Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
  }

  public function pauseContract(ChangeRequestStoreRequest $request, Contract $contract)
  {
    $pause_date = now();
    if ($request->pause_until == 'manual') {
      $data = [
        'pause_until' => 'manual',
        'description' => 'Pause Contract Until Manual Resume'
      ];
    } else if ($request->pause_until == 'custom_date') {
      $data = [
        'pause_until' => $request->custom_date_value,
        'description' => 'Pause Contract Until ' . date('d M, Y', strtotime($request->custom_date_value))
      ];
    } else if ($request->pause_until == 'custom_unit') {
      $data = [
        'pause_until' => now()->{'add' . $request->custom_unit}($request->pause_for),
        'description' => 'Pause Contract Until ' . now()->{'add' . $request->custom_unit}($request->pause_for)->format('d M, Y')
      ];
    } else if($request->pause_until == 'custom_date_from') {
      $data = [
        'pause_until' => 'manual',
        'description' => 'Pause Contract From ' . date('d M, Y', strtotime($request->custom_from_date_value)) . ' Until Manual Resume'
      ];
      $pause_date = $request->custom_from_date_value;
    }

    $data['action'] = 'Pause Contract';
    $data['pause_date'] = $pause_date;

    $contract->changeRequests()->create([
      'sender_type' => 'App\Models\Admin',
      'sender_id' => auth()->id(),
      'reason' => $request->reason,
      'type' => 'Lifecycle',
      'description' => $request->description,
      'data' => $data
    ]);

    return $this->sendRes('Change Request Added Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
  }
  public function resumeContract(ChangeRequestStoreRequest $request, Contract $contract)
  {
    $data = [
      'resume_date' => $request->resume_date == 'now' ? now() : $request->custom_resume_date,
      'description' => $request->resume_date == 'now' ? 'Resume Contract' : 'Schedule Contract To Resume',
      'action' => 'Resume'
    ];

    $contract->changeRequests()->create([
      'sender_type' => 'App\Models\Admin',
      'sender_id' => auth()->id(),
      'reason' => $request->reason,
      'description' => $request->description,
      'type' => 'Lifecycle',
      'data' => $data
    ]);

    return $this->sendRes('Change Request Added Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
  }
  public function terminateContract(ChangeRequestStoreRequest $request, Contract $contract)
  {
    $data = [
      'termination_date' => $request->terminate_date == 'now' ? now() : $request->custom_date,
      'description' => $request->terminate_date == 'now' ? 'Terminate Contract' : 'Schedule Contract For Termination',
      'action' => 'Termination'
    ];

    $contract->changeRequests()->create([
      'sender_type' => 'App\Models\Admin',
      'sender_id' => auth()->id(),
      'reason' => $request->reason,
      'type' => 'Lifecycle',
      'description' => $request->description,
      'data' => $data
    ]);

    return $this->sendRes('Change Request Added Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
  }

  public function approve(Contract $contract, ContractChangeRequest $changeRequest)
  {
    abort_if($changeRequest->status != 'Pending', 403, 'You can not reject this change request');

    /*
    * update the account balance of the contract and remaining amount
    */
    if ($contract->value != $changeRequest->new_value) {
      $this->updateAccountBalance($contract, $changeRequest);
    }

    $contract->update([
      'value' => $changeRequest->new_value,
      'currency' => $changeRequest->new_currency,
      'end_date' => $changeRequest->new_end_date,
    ]);

    $changeRequest->update([
      'status' => 'Approved',
      'reviewed_by' => auth()->id(),
      'reviewed_at' => now(),
    ]);

    return $this->sendRes('Change Request Approved Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table']);
  }

  protected function updateAccountBalance(Contract $contract, $changeRequest)
  {
    if ($contract->value != $changeRequest->new_value) {
      $this->transactionProcessor->create(
        AccountBalance::find($contract->account_balance_id),
        new TransactionDto(
          - ($changeRequest->new_value - $contract->value),
          $changeRequest->new_value > $contract->value ? 'Debit' : 'Credit',
          'Contract Commitment - Updated',
          '',
          [],
          ['type' => Contract::class, 'id' => $contract->id]
        )
      );
    }
  }

  public function reject(Contract $contract, ContractChangeRequest $changeRequest)
  {
    abort_if($changeRequest->status != 'Pending', 403, 'You can not reject this change request');

    $changeRequest->update([
      'status' => 'Rejected',
      'reviewed_by' => auth()->id(),
      'reviewed_at' => now(),
    ]);

    return $this->sendRes('Change Request Rejected Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table']);
  }

  public function destroy(Contract $contract, ContractChangeRequest $changeRequest)
  {
    $changeRequest->delete();

    return $this->sendRes('Change Request Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table']);
  }
}
