<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ContractsDataTable;
use App\Http\Controllers\Controller;
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
      ->selectRaw('count(case when deleted_at is null and end_date > now() then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and end_date <= now() then 1 end) as expired')
      ->selectRaw('count(case when deleted_at is null and end_date >= now() and end_date <= DATE_ADD(now(), INTERVAL 2 MONTH) then 1 end) as expiring_soon')
      ->selectRaw('count(case when deleted_at is null and created_at <= now() and created_at >= DATE_SUB(now(), INTERVAL 2 MONTH) then 1 end) as recently_added')
      ->selectRaw('count(case when deleted_at is not null then 1 end) as trashed')
      ->withTrashed()
      ->first();

    // top 5 contract types with highest number of contracts with % of contracts
    $data['contractTypes'] = ContractType::selectRaw('contract_types.name, count(contracts.id) as total, round(count(contracts.id) / (select count(*) from contracts where contracts.deleted_at Is NULL) * 100, 2) as percentage')
      ->leftJoin('contracts', 'contracts.type_id', '=', 'contract_types.id')
      ->whereNull('contracts.deleted_at')
      ->groupBy('contract_types.id')
      ->orderBy('total', 'desc')
      ->limit(5)
      ->get();

    // top 5 types with highest value of contracts with % of value
    $data['contractTypesValue'] = ContractType::selectRaw('contract_types.name, sum(contracts.value) as total, round(sum(contracts.value) / (select sum(value) from contracts where contracts.deleted_at Is NULL) * 100, 2) as percentage')
      ->leftJoin('contracts', 'contracts.type_id', '=', 'contract_types.id')
      ->whereNull('contracts.deleted_at')
      ->groupBy('contract_types.id')
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
    $data['statuses'] = Contract::getPossibleEnumValues('status');
    $data['contract'] = new Contract();

    if(request()->has('project')){
      $data['projects'] = Project::where('id', request()->project)->first();
      $data['companies'] = Company::where('id', $data['projects']->company_id)->pluck('name', 'id');
      $data['projects'] = [$data['projects']->id => $data['projects']->name];
    }else{
      $data['companies'] = Company::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Company'), '');
      $data['projects'] = [];
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.create', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'subject' => 'required|string|max:100',
      'type_id' => 'required|exists:contract_types,id',
      'company_id' => 'required|exists:companies,id',
      'project_id' => 'required|exists:projects,id',
      'status' => 'required|in:' . implode(',', Contract::getPossibleEnumValues('status')),
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'value' => 'required',
      'description' => 'required|string|max:1000',
    ]);

    Contract::create($request->all());

    return $this->sendRes(__('Contract created successfully'), ['event' => 'table_reload', 'table_id' => 'contracts-table', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(Contract $contract)
  {
    return view('admin.pages.contracts.show', ['contract' => $contract]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Contract $contract)
  {
    $contract->load('project');
    $data['types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Contract Type'), '');
    if($contract->project_id)
      $data['companies'] = Company::where('id', $contract->project->company_id)->pluck('name', 'id');
    else
      $data['companies'] = Company::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Company'), '');
    $data['statuses'] = Contract::getPossibleEnumValues('status');
    $data['contract'] = $contract;

    $data['projects'] = [$contract->project->id => $contract->project->name];

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.create', $data)->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Contract $contract)
  {
    $request->validate([
      'subject' => 'required|string|max:100',
      'type_id' => 'required|exists:contract_types,id',
      'company_id' => 'required|exists:companies,id',
      'project_id' => 'required|exists:projects,id',
      'status' => 'required|in:' . implode(',', Contract::getPossibleEnumValues('status')),
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'value' => 'required',
      'description' => 'required|string|max:1000',
    ]);

    $contract->update($request->all());

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
