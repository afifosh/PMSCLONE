<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ContractsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractStoreRequest;
use App\Http\Requests\Admin\ContractUpdateRequest;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Project;
use Illuminate\Http\Request;

class ContractController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ContractsDataTable $dataTable)
  {
    // get contracts count by end_date < now() as active, end_date >= now as expired, end_date - 2 months as expiring soon, start_date <= now, + 2 months as recently added
    $data['contracts'] = Contract::selectRaw('count(*) as total')
      ->selectRaw('count(case when deleted_at is null and status != "Draft" and end_date > now() then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status != "Draft" and end_date <= now() then 1 end) as expired')
      ->selectRaw('count(case when deleted_at is null and status != "Draft" and status !="Terminated" and end_date >= now() and end_date <= DATE_ADD(now(), INTERVAL 2 MONTH) then 1 end) as expiring_soon')
      ->selectRaw('count(case when deleted_at is null and status != "Draft" and created_at <= now() and created_at > DATE_SUB(now(), INTERVAL 1 Day) then 1 end) as recently_added')
      ->selectRaw('count(case when deleted_at is not null then 1 end) as trashed')
      ->selectRaw('count(case when deleted_at is null and status = "Draft" then 1 end) as draft')
      ->selectRaw('count(case when deleted_at is null and status = "Terminated" then 1 end) as terminateed')
      ->selectRaw('count(case when deleted_at is null and status = "Paused" then 1 end) as paused')
      ->selectRaw('(SELECT COUNT(DISTINCT contract_id) FROM contract_events WHERE event_type IN ("Extended", "Shortened", "Rescheduled", "Rescheduled And Amount Increased", "Rescheduled And Amount Decreased")) as rescheduled')
      ->withTrashed()
      ->first();

    // top 5 contract types with highest number of contracts with % of contracts
    $data['contractTypes'] = ContractType::selectRaw('contract_types.name, count(contracts.id) as total, round(count(contracts.id) / (select count(*) from contracts where contracts.deleted_at Is NULL) * 100, 2) as percentage')
      ->leftJoin('contracts', 'contracts.type_id', '=', 'contract_types.id')
      ->whereNull('contracts.deleted_at')
      ->groupBy('contract_types.id', 'contract_types.name')
      ->orderBy('total', 'desc')
      ->limit(5)
      ->get();

    // top 5 types with highest value of contracts with % of value
    $data['contractTypesValue'] = ContractType::selectRaw('contract_types.name, sum(contracts.value) as total, round(sum(contracts.value) / (select sum(value) from contracts where contracts.deleted_at Is NULL) * 100, 2) as percentage')
      ->leftJoin('contracts', 'contracts.type_id', '=', 'contract_types.id')
      ->whereNull('contracts.deleted_at')
      ->groupBy('contract_types.id', 'contract_types.name')
      ->orderBy('total', 'desc')
      ->limit(5)
      ->get();

    // top 5 companies by number of projects
    $data['companiesByProjects'] = Company:://has('contracts')->
    selectRaw('companies.name, count(contracts.id) as total, round(count(contracts.id) / (select count(*) from contracts where contracts.deleted_at Is NULL) * 100, 2) as percentage')
    ->leftJoin('contracts', function($join){
      $join->on('contracts.assignable_id', '=', 'companies.id')
      ->where('contracts.assignable_type', '=', Company::class);
    })
    ->whereNull('contracts.deleted_at')
    ->groupBy('companies.id', 'companies.name')
    ->orderBy('total', 'desc')
    ->limit(5)
    ->get();


    return $dataTable->render('admin.pages.contracts.index', $data);
    // view('admin.pages.contracts.index');
  }

  public function projectContractsIndex(ContractsDataTable $dataTable, Project $project)
  {
    $dataTable->projectId = $project->id;
    return $dataTable->render('admin.pages.contracts.index', ['project' => $project]);
    // view('admin.pages.contracts.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $data['types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Contract Type'), '');
    $data['contract'] = new Contract();
    $data['clients'] = Client::orderBy('id', 'desc')->pluck('email', 'id')->prepend(__('Select Client'), '');

    if(request()->has('project')){
      $data['projects'] = Project::where('id', request()->project)->first();
      $data['projects'] = Project::mine()->pluck('name', 'id')->prepend(__('Select Project'), '');
      $data['companies'] = ['' => 'Select Company'];
    }else{
      $data['projects'] = Project::mine()->pluck('name', 'id')->prepend(__('Select Project'), '');
      $data['companies'] = ['' => 'Select Company'];
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.create', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ContractStoreRequest $request)
  {
    $data['assignable_id'] = $request->assign_to == 'Client' ? $request->client_id : $request->company_id;
    $data['assignable_type'] = $request->assign_to == 'Client' ? Client::class : Company::class;

    if($request->isSavingDraft)
      $data['status'] = 'Draft';

    $contract = Contract::create($data + $request->validated());

    if(!$request->isSavingDraft)
    $contract->events()->create([
      'event_type' => 'Created',
      'modifications' => $request->validated(),
      'description' => 'Contract Created',
      'admin_id' => auth()->id(),
    ]);

    return $this->sendRes(__('Contract created successfully'), ['event' => 'table_reload', 'table_id' => 'contracts-table', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(Contract $contract)
  {
    $data['contract'] = $contract->load('notifiableUsers');
    $data['summary'] = $contract->events()->selectRaw('event_type, count(*) as total')->groupBy('event_type')->get();

    return view('admin.pages.contracts.show', $data);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Contract $contract)
  {
    $contract->load('project');
    $data['types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Contract Type'), '');
    $data['projects'] = Project::mine()->pluck('name', 'id')->prepend(__('Select Project'), '');
    $data['companies'] = Company::when($contract->assignable_type == Company::class, function($q) use ($contract){
      $q->where('id', $contract->assignable_id);
    })->pluck('name', 'id')->prepend(__('Select Company'), '');
    $data['contract'] = $contract;
    $data['clients'] = Client::orderBy('id', 'desc')->pluck('email', 'id')->prepend(__('Select Client'), '');
    $data['statuses'] = $contract->getPossibleStatuses();
    if($contract->status == 'Terminated')
      $data['termination_reason'] = $contract->getLatestTerminationReason();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.create', $data)->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(ContractUpdateRequest $request, Contract $contract)
  {
    if($contract->status == 'Draft' && !$request->isSavingDraft)
      $contract->events()->create([
        'event_type' => 'Created',
        'modifications' => $request->validated(),
        'description' => 'Contract Created',
        'admin_id' => auth()->id(),
      ]);
    else{
      if(!$request->isSavingDraft)
      if($request->start_date != $contract->start_date || $request->end_date != $contract->end_date || $request->value != $contract->value){
        $contract->saveEventLog($request, $contract);
      }
    }

    $data['assignable_id'] = $request->assign_to == 'Client' ? $request->client_id : $request->company_id;
    $data['assignable_type'] = $request->assign_to == 'Client' ? Client::class : Company::class;

    if($request->isSavingDraft)
      $data['status'] = 'Draft';
    else{
      if($contract->status == 'Draft')
        $data['status'] = 'Active';
    }

    $contract->update($data + $request->validated());

    return $this->sendRes(__('Contract updated successfully'), ['event' => 'table_reload', 'table_id' => 'contracts-table', 'close' => 'globalModal']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Contract $contract)
  {
    $contract->delete();

    return $this->sendRes(__('Contract deleted successfully'), ['event' => 'table_reload', 'table_id' => 'contracts-table']);
  }
}
