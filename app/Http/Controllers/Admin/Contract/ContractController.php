<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ContractsDataTable;
use App\DataTables\Admin\Contract\PaymentsPlanDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractStoreRequest;
use App\Http\Requests\Admin\ContractUpdateRequest;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Company;
use App\Models\ContractStage;
use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\ContractCategory;
use App\Models\ContractType;
use App\Models\Program;
use App\Models\Project;
use App\Support\LaravelBalance\Dto\TransactionDto;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Traits\FinanceTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Tax;
use DataTables;
use Illuminate\Http\Request;

class ContractController extends Controller
{
  use FinanceTrait;
  /**
   * Display a listing of the resource.
   */
  public function index(ContractsDataTable $dataTable)
  {
    $data['company'] = Company::find(request()->route('company'));
    $data['program'] = Program::find(request()->route('program'));

    if ($data['company']) {
        $dataTable->company = $data['company'];
    } if ($data['program']) {
      $dataTable->program = $data['program'];
    } else {
    $data['contract_statuses'] = ['0' => 'All'] + array_combine(Contract::STATUSES, Contract::STATUSES);
    $data['contractTypes'] = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');

    // get contracts count by end_date < now() as active, end_date >= now as expired, end_date - 2 months as expiring soon, start_date <= now, + 2 months as recently added
    $data['contracts'] = Contract::selectRaw('count(*) as total')
      // ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and DATE(end_date) > CURDATE() and DATE(end_date) > DATE_ADD(CURDATE(), INTERVAL 2 WEEK) then 1 end)) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and DATE(end_date) > CURDATE()) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and end_date <= now()) or (end_date is null and DATE(start_date) < CURDATE())) then 1 end) as expired')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and status !="Terminated" and end_date >= now() and end_date <= DATE_ADD(now(), INTERVAL 1 MONTH) then 1 end) as expiring_soon')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and start_date is not null and DATE(start_date) > CURDATE() then 1 end) as not_started')
      ->selectRaw('count(case when deleted_at is null and created_at <= now() and created_at > DATE_SUB(now(), INTERVAL 1 Day) then 1 end) as recently_added')
      ->selectRaw('count(case when deleted_at is not null then 1 end) as trashed')
      ->selectRaw('count(case when deleted_at is null and status = "Draft" then 1 end) as draft')
      ->selectRaw('count(case when deleted_at is null and status = "Terminated" then 1 end) as terminateed')
      ->selectRaw('count(case when deleted_at is null and status = "Paused" then 1 end) as paused')
      ->withTrashed()
      ->first();
    }

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
    $data['types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id');
    $data['categories'] = ContractCategory::orderBy('id', 'desc')->pluck('name', 'id');
    $data['contract'] = new Contract();
    $data['currency'] = [config('money.defaults.currency') => config('money.defaults.currencyText')];

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.create', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ContractStoreRequest $request)
  {
    $data['assignable_id'] = $request->company_id;
    $data['assignable_type'] = Company::class;
    $data['visible_to_client'] = $request->boolean('visible_to_client');

    if ($request->isSavingDraft)
      $data['status'] = 'Draft';

    $contract = Contract::create($data + $request->validated());

    if (!$request->isSavingDraft) {
      $this->transactionProcessor->create(
        AccountBalance::find($request->account_balance_id),
        new TransactionDto(
          -$request->value,
          'Debit',
          'Contract Commitment',
          '',
          [],
          ['type' => Contract::class, 'id' => $contract->id]
        )
      );

      $contract->events()->create([
        'event_type' => 'Created',
        'modifications' => $request->validated(),
        'description' => 'Contract Created',
        'admin_id' => auth()->id(),
      ]);
    }

    return $this->sendRes(__('Contract created successfully'), ['event' => 'table_reload', 'table_id' => 'contracts-table', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(Contract $contract)
  {
    if(request()->getjson){
      return response()->json($contract);
    }
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
    $data['types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id');
    $data['categories'] = ContractCategory::orderBy('id', 'desc')->pluck('name', 'id');
    $data['projects'] = $contract->project_id ? Project::where('id', $contract->project_id)->pluck('name', 'id') : [];
    $data['programs'] = $contract->program_id ? Program::where('id', $contract->program_id)->pluck('name', 'id') : [];
    $data['companies'] = Company::where('id', $contract->assignable_id)->pluck('name', 'id');
    $data['contract'] = $contract;
    $data['currency'] = [$contract->currency => '(' . $contract->currency . ') - ' . config('money.currencies.' . $contract->currency . '.name')];
    $data['statuses'] = $contract->getPossibleStatuses();
    $data['account_balanaces'] = $contract->account_balance_id ? AccountBalance::where('id', $contract->account_balance_id)->pluck('name', 'id') : [];
    if ($contract->status == 'Terminated')
      $data['termination_reason'] = $contract->getLatestTerminationReason();

      $userHasMarkedComplete = $contract->reviews->contains('user_id', auth()->id());
      $buttonLabel = $userHasMarkedComplete ? 'MARK AS UNREVIEWED' : 'MARK AS REVIEWED';
      $buttonIcon = $userHasMarkedComplete ? 'ti-undo' : 'ti-bell';
      $reviewStatus = $userHasMarkedComplete ? 'true' : 'false';
      $buttonLabelClass = $userHasMarkedComplete ? 'btn-label-danger' : 'btn-label-secondary';

      $modalTitle = '
      <h5 class="modal-title" id="globalModalTitle">Edit Contract</h5>
      <div class="flex items-center justify-between border-b-1 w-full">
          <button type="button" style=""
                  class="me-4 btn btn-sm rounded-pill ' . $buttonLabelClass . ' waves-effect"
                  data-contract-id="' . $contract->id . '"
                  data-is-reviewed="' . $reviewStatus . '"
                  onclick="toggleContractReviewStatus(this)">
              <span class="ti-xs ti ' . $buttonIcon . ' me-1"></span>' . $buttonLabel . '
          </button>
          <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"></button>
      </div>';


    return $this->sendRes('success', ['modaltitle' => $modalTitle, 'view_data' => view('admin.pages.contracts.create', $data)->render()]);


    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.create', $data)->render()]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function summaryTabContent(Contract $contract)
  {
    $contract->load('project');
    $data['types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id');
    $data['categories'] = ContractCategory::orderBy('id', 'desc')->pluck('name', 'id');
    $data['projects'] = $contract->project_id ? Project::where('id', $contract->project_id)->pluck('name', 'id') : [];
    $data['programs'] = $contract->program_id ? Program::where('id', $contract->program_id)->pluck('name', 'id') : [];
    $data['companies'] = Company::where('id', $contract->assignable_id)->pluck('name', 'id');
    $data['contract'] = $contract;
    $data['currency'] = [$contract->currency => '(' . $contract->currency . ') - ' . config('money.currencies.' . $contract->currency . '.name')];
    $data['statuses'] = $contract->getPossibleStatuses();
    $data['account_balanaces'] = $contract->account_balance_id ? AccountBalance::where('id', $contract->account_balance_id)->pluck('name', 'id') : [];
    if ($contract->status == 'Terminated')
      $data['termination_reason'] = $contract->getLatestTerminationReason();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.tabs.summary', $data)->render()]);
  }


    /**
     * Show the contracts summary.
     *
     * @return \Illuminate\Http\Response
     */
    public function showSummary(Contract $contract)
    {
      $contract->load('project');
      $data['types'] = ContractType::orderBy('id', 'desc')->pluck('name', 'id');
      $data['categories'] = ContractCategory::orderBy('id', 'desc')->pluck('name', 'id');
      $data['projects'] = $contract->project_id ? Project::where('id', $contract->project_id)->pluck('name', 'id') : [];
      $data['programs'] = $contract->program_id ? Program::where('id', $contract->program_id)->pluck('name', 'id') : [];
      $data['companies'] = Company::where('id', $contract->assignable_id)->pluck('name', 'id');
      $data['contract'] = $contract;
      $data['currency'] = [$contract->currency => '(' . $contract->currency . ') - ' . config('money.currencies.' . $contract->currency . '.name')];
      $data['statuses'] = $contract->getPossibleStatuses();
      $data['account_balanaces'] = $contract->account_balance_id ? AccountBalance::where('id', $contract->account_balance_id)->pluck('name', 'id') : [];
      if ($contract->status == 'Terminated')
        $data['termination_reason'] = $contract->getLatestTerminationReason();

        // Return a view with the summary data
        return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.tabs.summary', $data)->render()]);
        // Your logic to get summary data

    }

    /**
     * Show the contracts reviewers.
     *
     * @return \Illuminate\Http\Response
     */
    public function showReviewers(Contract $contract)
    {
        // Your logic to get reviewers data
        // ...

        // Prepare the data to be passed to the view
        $data = ['contract' => $contract]; // Ensuring the data is passed as a key-value pair

        // Render the 'reviewers' tab view to a string
        $viewRendered = view('admin.pages.contracts.tabs.reviewers', $data)->render();

        // Return the rendered view within a response
        return $this->sendRes('success', ['view_data' => $viewRendered]);

    }

    /**
     * Show the contracts comments.
     *
     * @return \Illuminate\Http\Response
     */
    public function showComments(Contract $contract)
    {
        // Your logic to get comments data
        // ...

        // Render the 'reviewers' tab view to a string
        $viewRendered = view('admin.pages.contracts.tabs.comments', compact('contract'))->render();

        // Return the rendered view within a response

        return $this->sendRes('success', ['view_data' => $viewRendered]);
    }

    /**
     * Show the contracts activities.
     *
     * @return \Illuminate\Http\Response
     */
    public function showActivities(Contract $contract)
    {
        // Your logic to get activities data
        // ...

        $contractAudits = \App\Models\Audit::where('auditable_id', $contract->id)
        ->where('auditable_type', get_class($contract))
        ->orderBy('created_at', 'desc')
        ->get();

        // Render the 'reviewers' tab view to a string
        $viewRendered = view('admin.pages.contracts.tabs.activities', compact('contractAudits'))->render();

        // Return the rendered view within a response
        return $this->sendRes('success', ['view_data' => $viewRendered]);
    }

  /**
   * Update the specified resource in storage.
   */
  public function update(ContractUpdateRequest $request, Contract $contract)
  {
    abort_if($contract->status != 'Draft' && $request->isSavingDraft, 400, 'You can not save draft for this contract');

    /*
    * if the contract is draft and now it is not draft then create transaction and event
    */
    if ($contract->status == 'Draft' && !$request->isSavingDraft) {
      $this->transactionProcessor->create(
        AccountBalance::find($request->account_balance_id),
        new TransactionDto(
          -$request->value,
          'Debit',
          'Contract Commitment',
          '',
          [],
          ['type' => Contract::class, 'id' => $contract->id]
        )
      );

      $contract->events()->create([
        'event_type' => 'Created',
        'modifications' => $request->validated(),
        'description' => 'Contract Created',
        'admin_id' => auth()->id(),
      ]);
    }

    $data['assignable_id'] = $request->company_id;
    $data['assignable_type'] = Company::class;
    $data['visible_to_client'] = $request->boolean('visible_to_client');

    if ($request->isSavingDraft)
      $data['status'] = 'Draft';
    else {
      if ($contract->status == 'Draft')
        $data['status'] = 'Active';
      else {
        // contract is not draft so gonna update the account and value of contract if it is changed
        $this->updateValueAndAccount($contract, $request);
      }
    }

    $contract->update($data + $request->validated());

    return $this->sendRes(__('Contract updated successfully'), ['event' => 'table_reload', 'table_id' => 'contracts-table']);
  }

  protected function updateValueAndAccount(Contract $contract, $request): void
  {
    // if the account_balance_id is null means added from seeder and now being updated from dashboard.
    // So first create the commitment transaction and then update the account_balance_id
    if($contract->account_balance_id == null){
      $this->transactionProcessor->create(
        AccountBalance::find($request->account_balance_id),
        new TransactionDto(
          -$contract->value,
          'Debit',
          'Contract Commitment',
          '',
          [],
          ['type' => Contract::class, 'id' => $contract->id]
        )
      );

      $contract->update(['account_balance_id' => $request->account_balance_id]);
    }

    // if only contract amount is changed then update the account transaction
    if ($contract->account_balance_id == $request->account_balance_id && $contract->value != $request->value) {
      $this->transactionProcessor->create(
        AccountBalance::find($request->account_balance_id),
        new TransactionDto(
          -($request->value - $contract->value),
          $request->value > $contract->value ? 'Debit' : 'Credit',
          'Contract Commitment - Updated',
          '',
          [],
          ['type' => Contract::class, 'id' => $contract->id]
        )
      );
    }else if($contract->account_balance_id != $request->account_balance_id){
      // if account is changed then do opposite transaction from old account and create new transaction in new account
      $this->transactionProcessor->create(
        AccountBalance::find($contract->account_balance_id),
        new TransactionDto(
          $contract->value,
          'Credit',
          'Contract Account Changed',
          'Contract Billing Account Changed so amount is credited back to old account',
          [],
          ['type' => Contract::class, 'id' => $contract->id]
        )
      );

      $this->transactionProcessor->create(
        AccountBalance::find($request->account_balance_id),
        new TransactionDto(
          -$request->value,
          'Debit',
          'Contract Commitment',
          'Contract Billing Account Changed so amount is debited from new account',
          [],
          ['type' => Contract::class, 'id' => $contract->id]
        )
      );
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Contract $contract)
  {
    $contract->delete();

    return $this->sendRes(__('Contract deleted successfully'), ['event' => 'table_reload', 'table_id' => 'contracts-table']);
  }

  public function releaseRetention(Contract $contract)
  {
    $contract->load('invoices');
    $contract->releaseInvoicesRetentions();

    return $this->sendRes(__('Retentions Released successfully'), ['event' => 'table_reload', 'table_id' => 'contracts-table']);
  }

  public function ContractPaymentsPlan(PaymentsPlanDataTable $dataTable)
  {

    $data['company'] = Company::find(request()->route('company'));
    $data['program'] = Program::find(request()->route('program'));
  // dd($data);
    if ($data['company']) {
        $dataTable->company = $data['company'];
    } if ($data['program']) {
      $dataTable->program = $data['program'];
    } else {
    $data['contract_statuses'] = ['0' => 'All'] + array_combine(Contract::STATUSES, Contract::STATUSES);
    $data['contractTypes'] = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');

    // get contracts count by end_date < now() as active, end_date >= now as expired, end_date - 2 months as expiring soon, start_date <= now, + 2 months as recently added
    $data['contracts'] = Contract::selectRaw('count(*) as total')
      // ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and DATE(end_date) > CURDATE() and DATE(end_date) > DATE_ADD(CURDATE(), INTERVAL 2 WEEK) then 1 end)) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and DATE(end_date) > CURDATE()) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and ((end_date is not null and end_date <= now()) or (end_date is null and DATE(start_date) < CURDATE())) then 1 end) as expired')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and status !="Terminated" and end_date >= now() and end_date <= DATE_ADD(now(), INTERVAL 1 MONTH) then 1 end) as expiring_soon')
      ->selectRaw('count(case when deleted_at is null and status = "Active" and start_date is not null and DATE(start_date) > CURDATE() then 1 end) as not_started')
      ->selectRaw('count(case when deleted_at is null and created_at <= now() and created_at > DATE_SUB(now(), INTERVAL 1 Day) then 1 end) as recently_added')
      ->selectRaw('count(case when deleted_at is not null then 1 end) as trashed')
      ->selectRaw('count(case when deleted_at is null and status = "Draft" then 1 end) as draft')
      ->selectRaw('count(case when deleted_at is null and status = "Terminated" then 1 end) as terminateed')
      ->selectRaw('count(case when deleted_at is null and status = "Paused" then 1 end) as paused')
      ->withTrashed()
      ->first();
    }

    return $dataTable->render('admin.pages.contracts.paymentsplan.index', $data);

  }

  public function ContractPaymentsPlanPhases($contract_id)
  {
    $query = ContractPhase::with(['stage:name,id']) // Load only the name of the related stage
    ->whereHas('stage.contract', function ($query) use ($contract_id) {
        $query->where('id', $contract_id);
    })
    ->select('contract_phases.*'); // Select all columns from contract_phases

    $query = ContractPhase::whereHas('stage.contract', function ($query) use ($contract_id) {
      $query->where('id', $contract_id);
  })->select('contract_phases.*');


      $dataTable = DataTables::of($query)
          ->addColumn('stage_name', function ($phase) {
            return $phase->stage ? $phase->stage->name : 'N/A'; // Added a null check in case a phase doesn't have a related stage
          })
          ->editColumn('invoice_id', function ($phase) {
              $invoiceItem = $phase->addedAsInvoiceItem->first();
              return $invoiceItem
                  ? '<a href="' . route('admin.invoices.edit', $invoiceItem->invoice_id) . '">' . runtimeInvIdFormat($invoiceItem->invoice_id) . '</a>'
                  : 'N/A';
          })
          ->addColumn('phase_name', function ($phase) {
              return $phase->name;
          })
          ->addColumn('start_date', function ($phase) {
              return $phase->start_date->format('d M, Y');
          })
          ->addColumn('due_date', function ($phase) {
              return $phase->due_date->format('d M, Y');
          })
          ->addColumn('amount', function ($phase) {
              return view('admin.pages.contracts.paymentsplan.value-column', compact('phase'));
          })
          ->editColumn('actions', function ($phase) use ($contract_id) {
              $is_editable = !(@$phase->addedAsInvoiceItem[0]->invoice->status && in_array(@$phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid']));
              return view('admin.pages.contracts.phases.actions', ['phase' => $phase, 'stage' => $phase->stage, 'contract_id' => $contract_id, 'is_editable' => $is_editable])->render();
          })
          ->addColumn('reviewed_by', function ($phase) {
            $reviewers = $phase->reviews;

            $html = '<div class="d-flex align-items-center avatar-group my-3">';

            $maxDisplayed = 5;
            for ($i = 0; $i < min($maxDisplayed, $reviewers->count()); $i++) {
                $reviewer = $reviewers[$i];
                $avatarUrl = $reviewer->user->avatar; // Assuming 'avatar' is the column name in the 'users' table
                $userName = htmlspecialchars($reviewer->user->name); // Escape the name to ensure it's safe to display

                $html .= '<div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" aria-label="' . $userName . '" data-bs-original-title="' . $userName . '">
                            <img src="' . $avatarUrl . '" alt="Avatar" class="rounded-circle">
                          </div>';
            }

            if ($reviewers->count() > $maxDisplayed) {
                $moreCount = $reviewers->count() - $maxDisplayed;
                $html .= '<div class="avatar pull-up">
                            <span class="avatar-initial rounded-circle" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" data-bs-original-title="' . $moreCount . ' more reviewers">+' . $moreCount . '</span>
                          </div>';
            }

            $html .= '</div>';
            return $html;
          })
          ->rawColumns(['actions', 'invoice_id', 'amount','reviewed_by']);

          $outputData = $dataTable->make(true)->getData(true); // Get data as an associative array

          // Add custom buttons to the data table's output
          $outputData['buttons'] = [
              [
                  'text' => 'Select Phases',
                  'className' => 'btn btn-primary mx-3 select-phases-btn',
                  'attr' => [
                      'onclick' => 'toggleCheckboxes()',
                  ],
              ],
              [
                  'text' => 'Create Invoices',
                  'className' => 'btn btn-primary mx-3 create-inv-btn d-none',
                  'attr' => [
                      'onclick' => 'createInvoices()',
                  ],
              ],
              [
                  'text' => 'Add Phase',
                  'className' => 'btn btn-primary',
                  'attr' => [
                      'data-toggle' => "ajax-modal",
                      'data-title' => 'Add Phase',
                      'data-href' => route('admin.projects.contracts.stages.phases.create', ['project' => 'project', $contract_id, $this->stage->id ?? 'stage']),
                  ],
              ],
          ];

          return response()->json($outputData); // Return a new JSON response with the modified data
  }

  public function ContractPaymentsPlanStages($contract_id)
  {
      $query = ContractStage::where('contract_id', $contract_id)
          ->withCount('phases')
          ->with(['contract' => function ($q) {
              $q->select(['contracts.id', 'currency']);
          }]);

      $dataTable = DataTables::of($query)
          ->editColumn('name', function ($stage) {
              return $stage->name;
          })
          ->editColumn('start_date', function($stage) {
              return $stage->start_date ? $stage->start_date->format('d M, Y') : '-';
          })
          ->editColumn('due_date', function($stage) {
              return $stage->due_date ? $stage->due_date->format('d M, Y') : '-';
          })
          ->addColumn('total_amount', function ($stage) {
            return cMoney($stage->stage_amount ?? 0, $stage->contract->currency, true);
          })
          ->addColumn('actions', function($stage){
            return view('admin.pages.contracts.stages.actions', compact('stage'));
          })
          ->filterColumn('total_amount', function ($query, $keyword) {
              $query->whereRaw("stage_amount like ?", ["%{$keyword}%"]);
          })
          ->filterColumn('phases_count', function ($query, $keyword) {
              $query->has('phases', $keyword);
          })
          ->rawColumns(['name', 'action']);

      return $dataTable->make(true);
  }

  public function toggleContractReviewStatus($contract_id)
  {
      try {
          // Find the contract by its ID
          $contract = Contract::findOrFail($contract_id);
          $table_id = 'contracts-table';
          // Check if the contract has already been reviewed by the current user
          $existingReview = $contract->reviews()->where('user_id', Auth::id())->first();
          if (request()->route()->named('contracts.paymentsplan')) {
            $table_id = 'payment-table';
          }

          if ($existingReview) {
              // The contract has already been reviewed by the current user.
              // Delete the review to mark the contract as "unreviewed"
              $existingReview->delete();

              return $this->sendRes('Contract marked as unreviewed!', ['event' => 'table_reload', 'table_id' => $table_id, 'close' => 'globalModal','isReviewed' => false]);
          } else {
              // Mark the contract as reviewed
              $review = new Review([
                  'user_id' => Auth::id(),
                  'reviewed_at' => now(),
              ]);
              $contract->reviews()->save($review);

              return $this->sendRes('Contract marked as reviewed!', ['event' => 'table_reload', 'table_id' => $table_id, 'close' => 'globalModal','isReviewed' => true]);
          }

      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
          return $this->sendError('Contract not found.');
      } catch (Throwable $e) {
          // It's good to log the actual error message in production for debugging.
          // Log::error($e->getMessage());
          return $this->sendError('Server Error');
      }
  }


  public function togglePhaseReviewStatus($contract_id, $phase_id)
  {
      try {
          // Find the contract by its ID
          $contract = Contract::findOrFail($contract_id);

          // Ensure the contract has a phase with the specified phase_id
          $phase = $contract->phases()->where('id', $phase_id)->first();

          if (!$phase) {
              // No such phase for this contract; handle the error accordingly.
              return $this->sendError('Invalid phase for this contract.');
          }

          // Check if the phase has already been reviewed by the current user
          $existingReview = $phase->reviews()->where('user_id', Auth::id())->first();

          if ($existingReview) {
              // The phase has already been reviewed by the current user.
              // Delete the review to mark the phase as "unreviewed"
              $existingReview->delete();
              return $this->sendRes('Phase marked as unreviewed!', ['isReviewed' => false]);
             // return $this->sendRes('Phase marked as unreviewed!');
          } else {
              // Mark the phase as reviewed
              $review = new Review([
                  'user_id' => Auth::id(),
                  'reviewed_at' => now(),
              ]);
              $phase->reviews()->save($review);

              //return $this->sendRes('Phase marked as reviewed!');
              return $this->sendRes('Phase marked as reviewed!', ['isReviewed' => true]);
          }

      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
          return $this->sendError('Contract not found.');
      } catch (Throwable $e) {
          return $this->sendError('Server Error');
      }
  }

  public function getContractsWithStagesAndPhases(Request $request)
  {
      $contracts = Contract::with(['stages.phases'])->get();

      $contractDataArray = $contracts->map(function ($contract) {
          return [
              'contract_name' => $contract->subject,
             // 'company' => $contract->company->name, // Assuming a 'company' relationship exists
           //   'start_date' => $contract->start_date->format('Y-m-d H:i:s'),
          //    'end_date' => $contract->due_date->format('Y-m-d H:i:s'),
              'value' => $contract->value,
              'stages' => $contract->stages->map(function ($stage) {
                  return [
                      'name' => $stage->name,
                      'phases' => $stage->phases->map(function ($phase) {
                          return ['name' => $phase->name];
                      })->toArray(),
                  ];
              })->toArray(),
          ];
      });

      return response()->json($contractDataArray);
  }

}
