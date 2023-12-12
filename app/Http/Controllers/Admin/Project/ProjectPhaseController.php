<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Contract\PhasesDataTable;
use App\Events\Admin\Contract\ContractUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\Phase\PhaseStoreRequest;
use App\Http\Requests\Admin\Contract\Phase\PhaseUpdateRequest;
use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\ContractStage;
use App\Models\InvoiceConfig;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectPhaseController extends Controller
{
  public function __construct()
  {
    $this->middleware('verifyContractNotTempered:phase,contract_id')->only(['show', 'edit', 'update', 'destroy']);
  }

  public function index($project, Contract $contract, string|ContractStage $stage, PhasesDataTable $dataTable)
  {
    $dataTable->stage = $stage;
    $dataTable->contract_id = $contract->id;
    $project = $contract->project ?? 'project';

    // abort_if(!$project->isMine(), 403);

    $page = 'Project';
    if (in_array(request()->route()->getName(), ['admin.contracts.stages.phases.index', 'admin.contracts.phases.index'])) {
      if (request()->route()->getName() == 'admin.contracts.stages.phases.index')
        $page = 'Contract';
      else
        $page = 'Contract All';
      $contract->load('notifiableUsers');
    }
    return $dataTable->render('admin.pages.contracts.phases.index', compact('contract', 'project', 'stage', 'page'));

    return view('admin.pages.contracts.phases.index', compact('contract', 'project', 'phase_statuses', 'colors', 'page', 'stage'));
  }

  public function contractPhases(Contract $contract, PhasesDataTable $dataTable, $stage = null,)
  {
    $stage = ContractStage::find($stage) ?? 'stage';

    return $this->index('project', $contract, $stage, $dataTable);
  }

  public function show($contract, Contractphase $phase)
  {
    if (request()->type == 'addons-list') {
      $phase->load(['taxes', 'deduction', 'contract.deductableDownpayments']);
      return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.show.show', compact('phase'))->render()]);
    }
  }

  public function create($project, Contract $contract, $stage)
  {
    $stage = ContractStage::find($stage) ?? 'stage';
    $contract->load('deductableDownpayments');
    $max_amount = $contract->remaining_amount;
    $phase = new ContractPhase();
    $tax_rates = InvoiceConfig::activeOnly()->get();
    $stages = $contract->stages->pluck('name', 'id');
    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'stages', 'phase', 'stage', 'max_amount', 'tax_rates'))->render()]);
  }

  public function store($project, Contract $contract, $stage, PhaseStoreRequest $request)
  {
    DB::beginTransaction();
    try {

      $data['total_cost'] = $request->estimated_cost;
      $stage = ContractStage::where('id', $request->stage_id)->firstOr(function () use ($contract, $request) {
        return ContractStage::create(['contract_id' => $contract->id, 'name' => $request->stage_id]);
      });
      $data['stage_id'] = $stage->id;

      $contract->phases()->create(
        $data + $request->only(['name', 'description', 'status', 'start_date', 'due_date', 'estimated_cost', 'stage_id'])
      );
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
    DB::commit();
    broadcast(new ContractUpdated($contract, 'phases'))->toOthers();

    return $this->sendRes(__('Phase Created Successfully'), ['event' => 'table_reload', 'table_id' => request()->tableId ? request()->tableId : 'phases-table', 'close' => 'globalModal']);
  }

  public function edit($project, $contract, $stage, ContractPhase $phase)
  {
    if (request()->tab == 'activity') {
      return $this->prepareActivityTab($phase);
    } else if (request()->tab == 'comments') {
      return $this->prepareCommentsTab($phase);
    } else if (request()->tab == 'reviewers'){
      return $this->sendRes('success', ['view_data' => 'Reviewers Tab']);
    }
    $phase->load(['addedAsInvoiceItem.invoice', 'contract']);
    $contract = $phase->contract;

    if (@$phase->addedAsInvoiceItem[0]->invoice->status && in_array($phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid'])) {
      return $this->sendError('You can not edit this phase because it is in paid invoice');
    }
    $stages = $contract->stages->pluck('name', 'id');
    $max_amount = $contract->remaining_amount + $phase->total_cost;
    $tax_rates = InvoiceConfig::activeOnly()->get();

    if (request()->type == 'edit-form') {
      return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create-form', compact('contract', 'stages', 'phase', 'stage', 'tax_rates', 'max_amount'))->render()]);
    }

    $userHasMarkedComplete = $phase->reviewdByAdmins()->where('admins.id', auth()->id())->exists();
    $buttonLabel = $userHasMarkedComplete ? 'MARK AS UNREVIEWED' : 'MARK AS REVIEWED';
    $buttonIcon = $userHasMarkedComplete ? 'ti-undo' : 'ti-bell';
    $reviewStatus = $userHasMarkedComplete ? 'true' : 'false';
    $buttonLabelClass = $userHasMarkedComplete ? 'btn-label-danger' : 'btn-label-secondary';

    $modalTitle = '
    <h5 class="modal-title" id="globalModalTitle">Edit Phase</h5>
    <div class="flex items-center justify-between border-b-1 w-full">
        <button type="button" style=""
                class="me-4 btn btn-sm rounded-pill ' . $buttonLabelClass . ' waves-effect"
                data-phase-id="' . $phase->id . '"
                data-contract-id="' . $contract->id . '"
                data-is-reviewed="' . $reviewStatus . '"
                onclick="togglePhaseReviewStatus(this)">
            <span class="ti-xs ti ' . $buttonIcon . ' me-1"></span>' . $buttonLabel . '
        </button>
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"></button>
    </div>';


    return $this->sendRes('success', ['modaltitle' => $modalTitle, 'view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'stages', 'phase', 'stage', 'tax_rates', 'max_amount'))->render()]);
  }

  public function prepareActivityTab($phase)
  {
    $contractAudits = \App\Models\Audit::where('auditable_id', $phase->id)
      ->where('auditable_type', get_class($phase))
      ->orderBy('created_at', 'desc')
      ->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.tab-activity', compact('contractAudits'))->render()]);
  }

  public function prepareCommentsTab($phase)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.tab-comments', compact('phase'))->render()]);
  }
  public function update($project, PhaseUpdateRequest $request, Contract $contract, $stage, ContractPhase $phase)
  {
    $phase->load(['addedAsInvoiceItem.invoice', 'stage']);

    if (@$phase->addedAsInvoiceItem[0]->invoice->status && in_array($phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid'])) {
      return $this->sendError('You can not update this phase because it is in paid invoice');
    }

    DB::beginTransaction();
    try {
      $old_cost = $phase->estimated_cost;
      $data['total_cost'] = $request->estimated_cost;
      $stage = ContractStage::where('id', $request->stage_id)->firstOr(function () use ($contract, $request) {
        return ContractStage::create(['contract_id' => $contract->id, 'name' => $request->stage_id]);
      });
      $data['stage_id'] = $stage->id;

      $phase->update($data + $request->only(['name', 'description', 'status', 'start_date', 'due_date', 'estimated_cost', 'stage_id']));

      if ($request->estimated_cost == 0) {
        $phase->taxes()->detach();
        $phase->deduction()->delete();
      } elseif ($old_cost != $request->estimated_cost) {
        $phase->reCalculateTaxAmountsAndResetManualAmounts(false);
        $phase->reCalculateTotal();
        $phase->recalculateDeductionAmount();
        $phase->reCalculateTotal();
        $phase->reCalculateTaxAmountsAndResetManualAmounts();
        $phase->reCalculateTotal();
        $phase->recalculateDeductionAmount();
        $phase->reCalculateTotal();
      }

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
    DB::commit();

    broadcast(new ContractUpdated($contract, 'phases'))->toOthers();

    return $this->sendRes(__('Phase Updated Successfully'), ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
  }

  public function destroy($project, Contract $contract, $stage, ContractPhase $phase)
  {
    $phase->load('addedAsInvoiceItem.invoice');
    if (@$phase->addedAsInvoiceItem[0]->invoice->status && in_array($phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid'])) {
      return $this->sendError('You can not delete this phase because it is in paid invoice');
    }

    $phase->addedAsInvoiceItem->each(function ($item) {
      $item->taxes()->detach();
      $item->delete();
      $item->deduction()->delete();
      $item->invoice->reCalculateTotal();
    });

    $phase->taxes()->detach();
    $phase->deduction()->delete();

    $phase->delete();

    broadcast(new ContractUpdated($contract, 'phases'))->toOthers();

    return $this->sendRes(__('Phase Deleted Successfully'), ['event' => 'table_reload', 'table_id' => 'phases-table', 'close' => 'globalModal']);
  }

  public function sortPhases($project, Contract $contract, Request $request)
  {

    $request->validate([
      'phases' => 'required|array',
      'phases.*' => 'required|integer|exists:contract_phases,id',
    ]);

    foreach ($request->phases as $order => $phase_id) {
      $contract->phases()->where('id', $phase_id)->update(['order' => $order]);
    }
    broadcast(new ContractUpdated($contract, 'phases'))->toOthers();

    return $this->sendRes(__('Phases Sorted Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList', 'close' => 'globalModal',]);
  }
}
