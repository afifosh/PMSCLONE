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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectPhaseController extends Controller
{
  public function index($project, Contract $contract, string|ContractStage $stage, PhasesDataTable $dataTable)
  {
    $dataTable->stage = $stage;
    $dataTable->contract_id = $contract->id;
    $project = $contract->project ?? 'project';

    // abort_if(!$project->isMine(), 403);

    $page = 'Project';
    if (request()->route()->getName() == 'admin.contracts.stages.phases.index') {
      $page = 'Contract';
      $contract->load('notifiableUsers');
    }
    return $dataTable->render('admin.pages.contracts.phases.index', compact('contract', 'project', 'stage', 'page'));

    return view('admin.pages.contracts.phases.index', compact('contract', 'project', 'phase_statuses', 'colors', 'page', 'stage'));
  }

  public function contractPhases(Contract $contract, $stage, PhasesDataTable $dataTable)
  {
    $stage = ContractStage::find($stage) ?? 'stage';

    return $this->index('project', $contract, $stage, $dataTable);
  }

  public function create($project, Contract $contract, $stage)
  {
    $stage = ContractStage::find($stage) ?? 'stage';
    $max_amount = $contract->remaining_amount;
    $phase = new ContractPhase();
    $tax_rates = InvoiceConfig::activeTaxes()->get();
    $stages = $contract->stages->pluck('name', 'id');
    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'stages', 'phase', 'stage', 'max_amount', 'tax_rates'))->render(), 'JsMethods' => ['initTaxRepeater']]);
  }

  public function store($project, Contract $contract, $stage, PhaseStoreRequest $request)
  {
    DB::beginTransaction();
    try{
      $data['total_cost'] = $request->estimated_cost + $request->total_tax_amount;
      $data['tax_amount'] = $request->total_tax_amount;
      $data['stage_id'] = $request->stage_id;

      $phase = $contract->phases()->create(
        $data + $request->only(['name', 'description', 'status', 'start_date', 'due_date', 'estimated_cost', 'stage_id'])
      );

      $this->storeTaxes($phase, $request);
    }catch(\Exception $e){
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
    DB::commit();
    broadcast(new ContractUpdated($contract, 'phases'))->toOthers();

    return $this->sendRes(__('Phase Created Successfully'), ['event' => 'table_reload', 'table_id' => request()->tableId ? request()->tableId : 'phases-table', 'close' => 'globalModal']);
  }

  protected function storeTaxes($phase, $request): void
  {
    $phase->taxes()->detach();
    foreach ($request->taxes as $tax) {
      $rate = $request->tax_rates->where('id', $tax['phase_tax'])->first();
      $calTaxAmount = $rate->type == 'Fixed' ? $rate->amount : ($rate->amount * $request->estimated_cost / 100);
      $phase->taxes()->attach($rate->id, [
        'amount' => $rate->getRawOriginal('amount'),
        'type' => $rate->type,
        'calculated_amount' => moneyToInt($calTaxAmount),
        'manual_amount' => $calTaxAmount == $tax['total_tax'] ? 0 : moneyToInt($tax['total_tax']),
        'pay_on_behalf' => $tax['pay_on_behalf'][0] ? 1 : 0,
        'is_authority_tax' => $tax['pay_on_behalf'][0] ? 1 : ($tax['is_authority_tax'][0] ? 1 : 0),
      ]);
    }
  }

  public function edit($project, $contract, $stage, ContractPhase $phase)
  {
    if (request()->tab == 'activity') {
      return $this->prepareActivityTab($phase);
    }else if(request()->tab == 'comments'){
      return $this->prepareCommentsTab($phase);
    }
    $phase->load(['addedAsInvoiceItem.invoice', 'contract']);
    $contract = $phase->contract;

    if (@$phase->addedAsInvoiceItem[0]->invoice->status && in_array($phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid'])) {
      return $this->sendError('You can not edit this phase because it is in paid invoice');
    }
    $stages = $contract->stages->pluck('name', 'id');
    $max_amount = $contract->remaining_amount + $phase->total_cost;
    $tax_rates = InvoiceConfig::activeTaxes()->get();

    $userHasMarkedComplete = $phase->reviews->contains('user_id', auth()->id());
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


    return $this->sendRes('success', ['modaltitle' => $modalTitle, 'view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'stages', 'phase', 'stage', 'tax_rates', 'max_amount'))->render(), 'JsMethods' => ['initTaxRepeater']]);
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
    try{
      $data['total_cost'] = $request->estimated_cost + $request->total_tax_amount;
      $data['tax_amount'] = $request->total_tax_amount;

      $phase->update($data + $request->only(['name', 'description', 'status', 'start_date', 'due_date', 'estimated_cost', 'stage_id']));

      // Check if there are taxes associated with the phase
      if ($phase->taxes->count() > 0) {
          $this->storeTaxes($phase, $request);        
      }      


      // if added in invoice then update invoice item and tax amount
      $phase->load('addedAsInvoiceItem.invoice');

      if ($phase->addedAsInvoiceItem->count()) {
        $phase->addedAsInvoiceItem->each(function ($item) use ($phase) {
          $item->update([
            'subtotal' => $phase->estimated_cost,
            'total_tax_amount' => $phase->tax_amount,
            'total' => $phase->total_cost,
            'rounding_amount' => $phase->rounding_amount,
          ]);

          $item->taxes()->detach();

          foreach ($phase->taxes as $tax) {
            $item->taxes()->attach($tax->id, [
              'amount' => $tax->pivot->amount,
              'type' => $tax->pivot->type,
              'invoice_id' => $item->invoice_id,
              'is_simple_tax' => 1,
              'calculated_amount' => $tax->pivot->calculated_amount,
              'manual_amount' => $tax->pivot->manual_amount,
              'pay_on_behalf' => $tax->pivot->pay_on_behalf,
              'is_authority_tax' => $tax->pivot->is_authority_tax
            ]);
          }

          $item->invoice->reCalculateTotal(); // ERROR HERE
          DB::commit();
        });
      }
    }catch(\Exception $e){
      DB::rollback();
      return $this->sendError($e->getMessage());
    }

    broadcast(new ContractUpdated($contract, 'phases'))->toOthers();

    return $this->sendRes(__('Phase Updated Successfully'), ['event' => 'table_reload',  'close' => 'globalModal', 'table_id' => request()->tableId ? request()->tableId : 'phases-table']);
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
    });

    $phase->taxes()->detach();

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
