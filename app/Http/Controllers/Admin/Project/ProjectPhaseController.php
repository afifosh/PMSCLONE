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
use App\Models\Tax;
use Illuminate\Http\Request;

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
    $tax_rates = Tax::where('is_retention', false)->where('status', 'Active')->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'phase', 'stage', 'max_amount', 'tax_rates'))->render()]);
  }

  public function store($project, Contract $contract, ContractStage $stage, PhaseStoreRequest $request)
  {
    $phase = $contract->phases()->create(
      ['stage_id' => $stage->id] + $request->only(['name', 'description', 'status', 'start_date', 'due_date', 'estimated_cost'])
    );
    $this->storeTaxes($phase, $request->phase_taxes);
    broadcast(new ContractUpdated($contract, 'phases'))->toOthers();

    return $this->sendRes(__('Phase Created Successfully'), ['event' => 'table_reload', 'table_id' => 'phases-table', 'close' => 'globalModal']);
  }

  protected function storeTaxes($phase, $taxes): void
  {
    $taxes = Tax::whereIn('id', filterInputIds($taxes))->where('is_retention', false)->where('status', 'Active')->get();

    $sync_data = [];
    foreach ($taxes as $rate) {
      $sync_data[$rate->id] = ['amount' => $rate->getRawOriginal('amount'), 'type' => $rate->type, 'contract_phase_id' => $phase->id];
    }

    $phase->taxes()->sync($sync_data);

    $phase->updateTaxAmount();
  }

  public function edit($project, Contract $contract, ContractStage $stage, ContractPhase $phase)
  {
    $phase->load(['addedAsInvoiceItem.invoice']);

    if (@$phase->addedAsInvoiceItem[0]->invoice->status && in_array($phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid'])) {
      return $this->sendError('You can not edit this phase because it is in paid invoice');
    }

    $max_amount = $contract->remaining_amount + $phase->total_cost;
    $tax_rates = Tax::where('is_retention', false)->where('status', 'Active')->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'phase', 'stage', 'tax_rates', 'max_amount'))->render()]);
  }

  public function update($project, PhaseUpdateRequest $request, Contract $contract, $stage, ContractPhase $phase)
  {
    $phase->load(['addedAsInvoiceItem.invoice', 'stage']);

    if (@$phase->addedAsInvoiceItem[0]->invoice->status && in_array($phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid'])) {
      return $this->sendError('You can not update this phase because it is in paid invoice');
    }

    $phase->update($request->only(['name', 'description', 'status', 'start_date', 'due_date', 'estimated_cost']));

    $this->storeTaxes($phase, $request->phase_taxes);

    // if added in invoice then update invoice item and tax amount
    $phase->load('addedAsInvoiceItem.invoice');

    if ($phase->addedAsInvoiceItem->count()) {
      $phase->addedAsInvoiceItem->each(function ($item) use ($phase) {
        $item->update(['amount' => $phase->estimated_cost]);

        $item->taxes()->detach();

        foreach($phase->taxes as $tax){
          $item->taxes()->attach($tax->id, ['amount' => $tax->pivot->amount, 'type' => $tax->pivot->type, 'invoice_id' => $item->invoice_id]);
        }

        $item->updateTaxAmount();

        $item->invoice->updateTaxAmount();
      });
    }

    broadcast(new ContractUpdated($contract, 'phases'))->toOthers();

    return $this->sendRes(__('Phase Updated Successfully'), ['event' => 'table_reload', 'table_id' => 'phases-table', 'close' => 'globalModal']);
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

    return $this->sendRes(__('Phases Sorted Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList']);
  }
}
