<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\ReviewChangeRequest;
use App\Models\Contract;
use App\Models\ContractChangeRequest;
use Illuminate\Support\Facades\DB;

class ChangeRequestReviewController extends Controller
{
  public function __construct()
  {
    // check if change request belongs to contract
    $this->middleware('verifyContractNotTempered:change_request,contract_id')->only(['destroy']);

    $this->middleware('permission:update contract')->only(['create', 'store']);
  }

  public function create(Contract $contract, ContractChangeRequest $changeRequest)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.change-requests.review', compact('contract', 'changeRequest'))->render()]);
  }

  public function store(Contract $contract, ContractChangeRequest $changeRequest, ReviewChangeRequest $request)
  {
    abort_if($changeRequest->status != 'Pending', 403, 'You can not review this change request');

    DB::beginTransaction();

    try {
      $contractData = [];
      if ($request->status == 'Approved') {
        if ($changeRequest->type == 'Terms') {
          $contract->update([
            'value' => $changeRequest->new_value,
            'currency' => $changeRequest->new_currency,
            'end_date' => $changeRequest->new_end_date,
          ]);
        } else if ($changeRequest->type == 'Lifecycle') {
          // validate action, with contract status
          if ($changeRequest->data['action'] == 'Pause Contract' && $contract->status != Contract::STATUSES[1]) {
            return $this->sendError('Contract is not active', ['show_alert' => true], 400);
          }else if ($changeRequest->data['action'] == 'Resume' && $contract->status != Contract::STATUSES[2]) {
            return $this->sendError('Contract is not paused', ['show_alert' => true], 400);
          }else if ($changeRequest->data['action'] == 'Termination' && $contract->status != Contract::STATUSES[1]) {
            return $this->sendError('Contract is not active', ['show_alert' => true], 400);
          }else if ($changeRequest->data['action'] == 'Early Completed' && $contract->status != Contract::STATUSES[1]) {
            return $this->sendError('Contract is not active', ['show_alert' => true], 400);
          }

          if ($changeRequest->data['action'] == 'Resume') {
            $contractData['start_date'] = $changeRequest->data['start_date'];
            $contractData['end_date'] = $changeRequest->data['end_date'];
          }
          $contract->update($contractData + [
            'status' => $this->resolveNewStatus($changeRequest->data['action']),
          ]);
        }
      }

      $changeRequest->update([
        'reviewed_at' => $request->reviewed_at,
        'reviewed_by' => auth()->id(),
        'status' => $request->status,
      ]);

      DB::commit();

      return $this->sendRes('success', ['event' => 'table_reload', 'table_id' => 'change-requests-table', 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendError($e->getMessage());
    }
  }

  private function resolveNewStatus($action)
  {
    if ($action == 'Pause Contract') {
      return 2;
    } else if ($action == 'Resume') {
      return 1;
    } else if ($action == 'Termination') {
      return 3;
    } else if ($action == 'Early Completed') {
      return 4;
    }
  }
}
