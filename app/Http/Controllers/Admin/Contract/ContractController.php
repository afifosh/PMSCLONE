<?php

namespace App\Http\Controllers\Admin\Contract;

use Akaunting\Money\Money;
use App\DataTables\Admin\Contract\ContractsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractStoreRequest;
use App\Http\Requests\Admin\ContractUpdateRequest;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractCategory;
use App\Models\ContractType;
use App\Models\Program;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ContractsDataTable $dataTable)
  {
    $data['contract_statuses'] = ['0' => 'All'] + array_combine(Contract::STATUSES, Contract::STATUSES);
    $data['projects'] = Project::mine()->whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');
    $data['contractTypes'] = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');
    $data['contractClients'] = Client::whereHas('contracts')->pluck('email', 'id')->prepend('All', '0');
    $data['companies'] = Company::has('contracts')->get(['id', 'name', 'type']);;
    $data['programs'] = Program::has('contracts')->pluck('name', 'id')->prepend('All', '0');

    // get contracts count by end_date < now() as active, end_date >= now as expired, end_date - 2 months as expiring soon, start_date <= now, + 2 months as recently added
    $data['contracts'] = Contract::selectRaw('count(*) as total')
      // ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and DATE(end_date) > CURDATE() and DATE(end_date) > DATE_ADD(CURDATE(), INTERVAL 2 WEEK) then 1 end)) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and DATE(end_date) > CURDATE()) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and end_date <= now()) or (end_date is null and DATE(start_date) < CURDATE())) then 1 end) as expired')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and status !="Terminated" and end_date >= now() and end_date <= DATE_ADD(now(), INTERVAL 2 WEEK) then 1 end) as expiring_soon')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and start_date is not null and DATE(start_date) > CURDATE() then 1 end) as not_started')
      ->selectRaw('count(case when deleted_at is null and created_at <= now() and created_at > DATE_SUB(now(), INTERVAL 1 Day) then 1 end) as recently_added')
      ->selectRaw('count(case when deleted_at is not null then 1 end) as trashed')
      ->selectRaw('count(case when deleted_at is null and status = "Draft" then 1 end) as draft')
      ->selectRaw('count(case when deleted_at is null and status = "Terminated" then 1 end) as terminateed')
      ->selectRaw('count(case when deleted_at is null and status = "Paused" then 1 end) as paused')
      ->selectRaw('(SELECT COUNT(DISTINCT contract_id) FROM contract_events WHERE event_type Like "%Revised%" OR event_type Like "%Rescheduled%") as rescheduled')
      ->withTrashed()
      ->first();

    return $dataTable->render('admin.pages.contracts.index', $data);
    // view('admin.pages.contracts.index');
  }

  public function statistics()
  {
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
    $data['companiesByProjects'] = $this->getCompaniesByProjects();


    // top 5 contracts by value
    $data['contractsByValue'] = $this->getContractsByValue();

    // contracts by cycle time: less than 3 months, 3 months to 1 year, 1 year to 2 years, more than 2 years
    $data['contractsByCycleTime'] = $this->getContractsByCycleTime();

    // contracts count expiring in 30,60,90 days
    $data['contractsByExpiryTime'] = $this->getContractsByExpiryTime();

    // contracts by status
    $data['contractsByStatus'] = $this->getContractsByStatus();

    $data['contractsByDistribution'] = $this->getContractsByAssignees();

    // list of contracts expiring in 30, 60, 90 days
    $data['expiringContractsList'] = Contract::where('status', 'Active')
      ->whereNotNull('end_date')
      ->where('end_date', '>', now())
      ->where('end_date', '<', now()->addDays(90))
      ->get();
    return view('admin.pages.contracts.statistics', $data);
  }

  protected function getContractsByAssignees()
  {
    // select contracts group by assignable_type
    $data = Contract::whereNotNull('assignable_type')->selectRaw('assignable_type, COUNT(*) as contract_count')
      ->groupBy('assignable_type')
      ->get();

    $data = $data->toArray();

    return $data;
  }

  protected function getContractsByStatus()
  {
    $contracts = Contract::selectRaw('count(*) as total')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and DATE(end_date) > CURDATE()) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as Active')
      ->selectRaw('count(case when deleted_at is null and status = "Paused" then 1 end) as Paused')
      ->selectRaw('count(case when deleted_at is null and status = "Draft" then 1 end) as Draft')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and end_date <= now()) or (end_date is null and DATE(start_date) < CURDATE())) then 1 end) as Expired')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and start_date is not null and DATE(start_date) > CURDATE() then 1 end) as Not_Started')
      ->selectRaw('count(case when deleted_at is null and status = "Terminated" then 1 end) as Terminateed')
      ->selectRaw('count(case when deleted_at is not null then 1 end) as Trashed')
      ->withTrashed()
      ->first();

    $contracts = $contracts->toArray();
    unset($contracts['status'], $contracts['printable_value']);

    return $contracts;
  }

  protected function getContractsByValue()
  {
    $data['contractsByValue'] = Contract::selectRaw('id, contracts.subject, contracts.value, contracts.currency')
      ->whereNull('contracts.deleted_at')
      ->orderBy('contracts.value', 'desc')
      ->limit(5)
      ->get();

    $data['contractsByValue'] = array_merge($data['contractsByValue']->toArray(), array_fill(0, 5 - count($data['contractsByValue']), ['id' => '', 'subject' => '', 'value' => 0,]));

    return $data['contractsByValue'];
  }

  protected function getCompaniesByProjects()
  {
    $data['companiesByProjects'] = Company::selectRaw(
      'companies.name,
    sum(case when contracts.assignable_type = ? then 1 else 0 end) as total,
    round(sum(case when contracts.assignable_type = ? then 1 else 0 end) /
          (select count(*) from contracts where contracts.deleted_at Is NULL and contracts.assignable_type = ?) * 100, 2) as percentage',
      [Company::class, Company::class, Company::class]
    )
      ->leftJoin('contracts', function ($join) {
        $join->on('contracts.assignable_id', '=', 'companies.id')
          ->where('contracts.assignable_type', '=', Company::class);
      })
      ->whereNull('contracts.deleted_at')
      ->groupBy('companies.id', 'companies.name')
      ->orderBy('total', 'desc')
      ->limit(5)
      ->get();

    // data must have 5 indexs
    $data['companiesByProjects'] = array_merge($data['companiesByProjects']->toArray(), array_fill(0, 5 - count($data['companiesByProjects']), ['name' => '', 'total' => 0, 'percentage' => 0]));

    return $data['companiesByProjects'];
  }

  protected function getContractsByCycleTime()
  {
    $data['contractsByCycleTime'] = Contract::select(
      DB::raw('CASE
          WHEN DATEDIFF(end_date, start_date) < 90 THEN "<90_Days"
          WHEN DATEDIFF(end_date, start_date) >= 90 AND DATEDIFF(end_date, start_date) <= 365 THEN "1_Year"
          WHEN DATEDIFF(end_date, start_date) > 365 AND DATEDIFF(end_date, start_date) <= 730 THEN "2_Years"
          WHEN DATEDIFF(end_date, start_date) > 730 THEN "2_Years+"
          Else "unknown"
      END as time_period'),
      DB::raw('COUNT(*) as contract_count')
    )
      ->groupBy('time_period')
      ->get();

    foreach ($data['contractsByCycleTime'] as $key => $value) {
      $data['contractsByCycleTime'][$key]['time_period'] = str_replace('_', ' ', $value['time_period']);
    }
    // set default values for time periods with time period name and count 0
    $timePeriods = ['<90 Days', '1 Year', '2 Years', '2 Years+'];
    foreach ($timePeriods as $timePeriod) {
      if (!isset($data['contractsByCycleTime']->where('time_period', $timePeriod)->first()->time_period)) {
        $data['contractsByCycleTime'][] = ['time_period' => $timePeriod, 'contract_count' => 0];
      }
    }

    // sort by timePeriods array
    $data['contractsByCycleTime'] = $data['contractsByCycleTime']->sortBy(function ($model) use ($timePeriods) {
      return array_search($model['time_period'], $timePeriods);
    });

    $data['contractsByCycleTime'] = $data['contractsByCycleTime']->values()->all();
    // remove unknown collection
    $data['contractsByCycleTime'] = collect($data['contractsByCycleTime'])->filter(function ($value, $key) {
      return $value['time_period'] != 'unknown';
    })->values()->all();

    return $data['contractsByCycleTime'];
  }

  protected function getContractsByExpiryTime()
  {
    $data['contractsByExpiryTime'] = Contract::select(
      DB::raw('CASE
          WHEN DATEDIFF(end_date, now()) <= 30 THEN "30_days"
          WHEN DATEDIFF(end_date, now()) > 30 AND DATEDIFF(end_date, now()) <= 60 THEN "60_days"
          WHEN DATEDIFF(end_date, now()) > 60 AND DATEDIFF(end_date, now()) <= 90 THEN "90_days"
      END as time_period'),
      DB::raw('COUNT(*) as contract_count')
    )
      ->where('status', 'Active')
      ->where('end_date', '>', now())
      ->where('end_date', '<=', now()->addDays(90))
      ->groupBy('time_period')
      ->get();

    $data['contractsByExpiryTime'] = $data['contractsByExpiryTime']->whereNotNull('time_period');

    foreach ($data['contractsByExpiryTime'] as $key => $value) {
      $data['contractsByExpiryTime'][$key]['time_period'] = str_replace('_', ' ', $value['time_period']);
    }
    // set default values for time periods with time period name and count 0
    $timePeriods = ['30 days', '60 days', '90 days'];
    foreach ($timePeriods as $timePeriod) {
      if (!isset($data['contractsByExpiryTime']->where('time_period', $timePeriod)->first()->time_period)) {
        $data['contractsByExpiryTime'][] = ['time_period' => $timePeriod, 'contract_count' => 0];
      }
    }

    // sort by timePeriods array
    $data['contractsByExpiryTime'] = $data['contractsByExpiryTime']->sortBy(function ($model) use ($timePeriods) {
      return array_search($model['time_period'], $timePeriods);
    });

    $data['contractsByExpiryTime'] = $data['contractsByExpiryTime']->values()->all();
    return $data['contractsByExpiryTime'];
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
    $data['categories'] = ContractCategory::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Category'), '');
    $data['contract'] = new Contract();
    $data['clients'] = ['' => __('Select Client')];
    $data['currency'] = ['USD' => '(USD) - US Dollar'];

    $data['projects'] = ['' => __('Select Project')];
    $data['companies'] = ['' => 'Select Client'];
    $data['programs'] = ['' => 'Select Program'];

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.create', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ContractStoreRequest $request)
  {
    $data['assignable_id'] = $request->company_id;
    $data['assignable_type'] = Company::class;

    if ($request->isSavingDraft)
      $data['status'] = 'Draft';

    $contract = Contract::create($data + $request->validated());

    if (!$request->isSavingDraft){
      $contract->events()->create([
        'event_type' => 'Created',
        'modifications' => $request->validated(),
        'description' => 'Contract Created',
        'admin_id' => auth()->id(),
      ]);

      $contract->phases()->create([
        'name' => 'Phase 1'
      ]);
    }

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
    $data['categories'] = ContractCategory::orderBy('id', 'desc')->pluck('name', 'id')->prepend(__('Select Category'), '');
    $data['projects'] = $contract->project_id ? Project::where('id', $contract->project_id)->pluck('name', 'id') : ['' => __('Select Project')];
    $data['programs'] = $contract->program_id ? Program::where('id', $contract->program_id)->pluck('name', 'id') : ['' => __('Select program')];
    $data['companies'] = Company::where('id', $contract->assignable_id)->pluck('name', 'id')->prepend('Select Client', '');
    $data['contract'] = $contract;
    $data['currency'] = [$contract->currency => '(' . $contract->currency . ') - ' . config('money.currencies.' . $contract->currency. '.name')];
    $data['statuses'] = $contract->getPossibleStatuses();
    if ($contract->status == 'Terminated')
      $data['termination_reason'] = $contract->getLatestTerminationReason();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.create', $data)->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(ContractUpdateRequest $request, Contract $contract)
  {
    if ($contract->status == 'Draft' && !$request->isSavingDraft)
      $contract->events()->create([
        'event_type' => 'Created',
        'modifications' => $request->validated(),
        'description' => 'Contract Created',
        'admin_id' => auth()->id(),
      ]);
    // else {
    //   if (!$request->isSavingDraft)
    //     if ($request->start_date != $contract->start_date || $request->end_date != $contract->end_date || $request->value != $contract->value) {
    //       $contract->saveEventLog($request, $contract);
    //     }
    // }

    $data['assignable_id'] = $request->company_id;
    $data['assignable_type'] = Company::class;

    if ($request->isSavingDraft)
      $data['status'] = 'Draft';
    else {
      if ($contract->status == 'Draft')
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
