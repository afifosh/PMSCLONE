<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ChangeRequestsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\ChangeRequestStoreRequest;
use App\Models\Contract;
use App\Models\ContractChangeRequest;

class ChangeRequestController extends Controller
{
  public function __construct()
  {
    // check if change request belongs to contract
    $this->middleware('verifyContractNotTempered:change_request,contract_id')->only(['destroy']);

    $this->middleware('permission:read contract')->only(['index']);
    $this->middleware('permission:update contract')->only(['create', 'store', 'update']);
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
    } else if($request->action_type == 'early-completed-contract') {
      return $this->earlyCompletedContract($request, $contract);
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

  private function earlyCompletedContract(ChangeRequestStoreRequest $request, Contract $contract)
  {
    $data = [
      'action' => 'Early Completed',
    ];

    $contract->changeRequests()->create([
      'sender_type' => 'App\Models\Admin',
      'sender_id' => auth()->id(),
      'reason' => $request->reason,
      'description' => $request->description,
      'type' => 'Lifecycle',
      'data' => $data,
      'requested_at' => $request->requested_at
    ]);

    return $this->sendRes('Change Request Added Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
  }

  public function pauseContract(ChangeRequestStoreRequest $request, Contract $contract)
  {
    // $pause_date = now();
    // if ($request->pause_until == 'manual') {
    //   $data = [
    //     'pause_until' => 'manual',
    //     'description' => 'Pause Contract Until Manual Resume'
    //   ];
    // } else if ($request->pause_until == 'custom_date') {
    //   $data = [
    //     'pause_until' => $request->custom_date_value,
    //     'description' => 'Pause Contract Until ' . date('d M, Y', strtotime($request->custom_date_value))
    //   ];
    // } else if ($request->pause_until == 'custom_unit') {
    //   $data = [
    //     'pause_until' => now()->{'add' . $request->custom_unit}($request->pause_for),
    //     'description' => 'Pause Contract Until ' . now()->{'add' . $request->custom_unit}($request->pause_for)->format('d M, Y')
    //   ];
    // } else if($request->pause_until == 'custom_date_from') {
    //   $data = [
    //     'pause_until' => 'manual',
    //     'description' => 'Pause Contract From ' . date('d M, Y', strtotime($request->custom_from_date_value)) . ' Until Manual Resume'
    //   ];
    //   $pause_date = $request->custom_from_date_value;
    // }

    $data['action'] = 'Pause Contract';
    // $data['pause_date'] = $pause_date;

    $contract->changeRequests()->create([
      'sender_type' => 'App\Models\Admin',
      'sender_id' => auth()->id(),
      'reason' => $request->reason,
      'type' => 'Lifecycle',
      'description' => $request->description,
      'data' => $data,
      'requested_at' => $request->requested_at
    ]);

    return $this->sendRes('Change Request Added Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
  }
  public function resumeContract(ChangeRequestStoreRequest $request, Contract $contract)
  {
    $data = [
      // 'resume_date' => $request->resume_date == 'now' ? now() : $request->custom_resume_date,
      // 'description' => $request->resume_date == 'now' ? 'Resume Contract' : 'Schedule Contract To Resume',
      'action' => 'Resume',
      'start_date' => $request->start_date,
      'end_date' => $request->end_date
    ];

    $contract->changeRequests()->create([
      'sender_type' => 'App\Models\Admin',
      'sender_id' => auth()->id(),
      'reason' => $request->reason,
      'description' => $request->description,
      'type' => 'Lifecycle',
      'data' => $data,
      'requested_at' => $request->requested_at
    ]);

    return $this->sendRes('Change Request Added Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
  }
  public function terminateContract(ChangeRequestStoreRequest $request, Contract $contract)
  {
    $data = [
      // 'termination_date' => $request->terminate_date == 'now' ? now() : $request->custom_date,
      // 'description' => $request->terminate_date == 'now' ? 'Terminate Contract' : 'Schedule Contract For Termination',
      'action' => 'Termination'
    ];

    $contract->changeRequests()->create([
      'sender_type' => 'App\Models\Admin',
      'sender_id' => auth()->id(),
      'reason' => $request->reason,
      'type' => 'Lifecycle',
      'description' => $request->description,
      'data' => $data,
      'requested_at' => $request->requested_at
    ]);

    return $this->sendRes('Change Request Added Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
  }

  public function destroy(Contract $contract, ContractChangeRequest $changeRequest)
  {
    $changeRequest->delete();

    return $this->sendRes('Change Request Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'change-requests-table']);
  }
}
