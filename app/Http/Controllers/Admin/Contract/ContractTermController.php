<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Program;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractTermController extends Controller
{
  public function edit(Contract $contract)
  {
    $contract->load('project');
    $data['types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Contract Type'), '');
    $data['projects'] = $contract->project_id ? Project::where('id', $contract->project_id)->pluck('name', 'id') : ['' => __('Select Project')];
    $data['programs'] = $contract->program_id ? Program::where('id', $contract->program_id)->pluck('name', 'id') : ['' => __('Select program')];
    $data['companies'] = Company::where('id', $contract->assignable_id)->pluck('name', 'id')->prepend('Select Client', '');
    $data['contract'] = $contract;
    $data['currency'] = [$contract->currency => '(' . $contract->currency . ') - ' . config('money.currencies.' . $contract->currency. '.name')];
    $data['statuses'] = $contract->getPossibleStatuses();
    if ($contract->status == 'Terminated')
      $data['termination_reason'] = $contract->getLatestTerminationReason();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.terms.edit', $data)->render()]);
    return view('admin.pages.contracts.terms.edit', compact('contract'));
  }

  public function update(Request $request, Contract $contract)
  {
    if($contract->status == 'Draft')
      return $this->sendError(__('You can not update draft contract'), ['event' => 'table_reload', 'table_id' => 'events-table', 'close' => 'globalModal'], 200);
    $att = $request->validate([
      'start_date' => 'nullable|required_if:isSavingDraft,0|date',
      'end_date' => 'nullable|date|after_or_equal:start_date',
      // 'currency' => ['required', Rule::In(array_keys(config('money.currencies')))],
      'value' => ['nullable', Rule::requiredIf(!$request->isSavingDraft || $contract->status != 'Draft'), 'min:0', 'max:92233720368547758'],
    ]);

    $contract->saveEventLog($request, $contract);

    $contract->update($att);

    return $this->sendRes(__('Contract updated successfully'), ['event' => 'table_reload', 'table_id' => 'events-table', 'close' => 'globalModal']);
  }
}
