<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ContractsDataTable;
use App\DataTables\Admin\Contract\ContractsTrackingDataTable;
use App\DataTables\Admin\Contract\PaymentsPlanDataTable;
use App\DataTables\Admin\Contract\TrackingPaymentsPlanDataTable;
use App\DataTables\Admin\Contract\ContractPaymentsPlanReviewDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractStoreRequest;
use App\Http\Requests\Admin\ContractUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Admin;
use App\Models\Company;
use App\Models\ContractStage;
use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\ContractCategory;
use App\Models\ContractType;
use App\Models\Program;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Http\Request;

class ContractController extends Controller
{
  public function __construct()
  {
    $this->middleware('permission:read contract', ['only' => [
      'index', 'show', 'statistics', 'projectContractsIndex',
      'trackingPaymentsPlan', 'trackingContract', 'showSummary',
      'showReviewers', 'showComments', 'showActivities', 'summaryTabContent',
      'ContractPaymentsPlan', 'ContractPaymentsPlanReview',
      'ContractPaymentsPlanPhases', 'ContractPaymentsPlanStages'
      ]]);
    $this->middleware('permission:create contract', ['only' => ['create', 'store']]);
    $this->middleware('permission:update contract', ['only' => ['edit', 'update', 'togglePhaseReviewStatus', 'toggleContractReviewStatus', 'releaseRetention']]);
    $this->middleware('permission:delete contract', ['only' => ['destroy']]);
  }

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
    $data['contract_statuses'] = ['' => 'All'] + Contract::STATUSES;
    $data['contractTypes'] = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');

    // get contracts count by end_date < now() as active, end_date >= now as expired, end_date - 2 months as expiring soon, start_date <= now, + 2 months as recently added
    $data['contracts'] = Contract::selectRaw('count(*) as total')
      // ->selectRaw('count(case when deleted_at is null and status = "1" and ((end_date is not null and DATE(end_date) > CURDATE() and DATE(end_date) > DATE_ADD(CURDATE(), INTERVAL 2 WEEK) then 1 end)) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status = "1" and ((end_date is not null and DATE(end_date) > CURDATE()) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as active')
      ->selectRaw('count(case when deleted_at is null and status = "1" and ((end_date is not null and end_date <= now()) or (end_date is null and DATE(start_date) < CURDATE())) then 1 end) as expired')
      ->selectRaw('count(case when deleted_at is null and status = "1" and status !="3" and end_date >= now() and end_date <= DATE_ADD(now(), INTERVAL 1 MONTH) then 1 end) as expiring_soon')
      ->selectRaw('count(case when deleted_at is null and status = "1" and start_date is not null and DATE(start_date) > CURDATE() then 1 end) as not_started')
      ->selectRaw('count(case when deleted_at is null and created_at <= now() and created_at > DATE_SUB(now(), INTERVAL 1 Day) then 1 end) as recently_added')
      ->selectRaw('count(case when deleted_at is not null then 1 end) as trashed')
      ->selectRaw('count(case when deleted_at is null and status = "0" then 1 end) as draft')
      ->selectRaw('count(case when deleted_at is null and status = "3" then 1 end) as terminateed')
      ->selectRaw('count(case when deleted_at is null and status = "2" then 1 end) as paused')
      ->withTrashed()
      ->first();
    }

    return $dataTable->render('admin.pages.contracts.index', $data);
    // view('admin.pages.contracts.index');
  }

  public function trackingPaymentsPlan(TrackingPaymentsPlanDataTable $dataTable)
  {
    $data['contract_statuses'] = ['' => 'All'] + Contract::STATUSES;
    $data['contractTypes'] = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');
    $data['review_status'] = [
      '' => 'Select Status',
      'reviewed' => 'Reviewed',
      'not_reviewed' => 'Not Reviewed',
      'partially_reviewed' => 'Partially Reviewed',
    ];

    return $dataTable->render('admin.pages.contracts.tracking.paymentsplan.index', $data);
    // view('admin.pages.contracts.tracking.paymentsplan.index');
  }

  public function trackingContract(ContractsTrackingDataTable $dataTable)
  {
    $data['contract_statuses'] = ['' => 'All'] + Contract::STATUSES;
    $data['contractTypes'] = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');

    // Prepare the options for the dropdown
    $data['review_status'] = [
      '' => 'Select Status',
      'reviewed' => 'Reviewed',
      'not_reviewed' => 'Not Reviewed'
    ];

    return $dataTable->render('admin.pages.contracts.tracking.contracts.index', $data);
    // view('admin.pages.contracts.tracking.contracts.index')
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
    $data['expiringContractsList'] = Contract::where('status', '1')
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
      ->selectRaw('count(case when deleted_at is null and status = "1" and ((end_date is not null and DATE(end_date) > CURDATE()) or (end_date is null and DATE(start_date) = CURDATE())) then 1 end) as Active')
      ->selectRaw('count(case when deleted_at is null and status = "2" then 1 end) as 2')
      ->selectRaw('count(case when deleted_at is null and status = "0" then 1 end) as Draft')
      ->selectRaw('count(case when deleted_at is null and status = "1" and ((end_date is not null and end_date <= now()) or (end_date is null and DATE(start_date) < CURDATE())) then 1 end) as Expired')
      ->selectRaw('count(case when deleted_at is null and status = "1" and start_date is not null and DATE(start_date) > CURDATE() then 1 end) as Not_Started')
      ->selectRaw('count(case when deleted_at is null and status = "3" then 1 end) as Terminateed')
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
      ->where('status', '1')
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
      $data['status'] = '0';

    $contract = Contract::create($data + $request->validated());

    if (!$request->isSavingDraft) {
      $contract->events()->create([
        'event_type' => 'Created',
        'modifications' => $request->validated(),
        'description' => 'Contract Created',
        'admin_id' => auth()->id(),
      ]);
    }

    return $this->sendRes(__('Contract created successfully'), ['event' => 'functionCall', 'function' => 'reloadDataTables', 'close' => 'globalModal']);
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
    $data['sankey_funds_data'] = $contract->getSankeyFundsData();

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
    if ($contract->status == '3')
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
    if ($contract->status == '3')
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
      if ($contract->status == '3')
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

    abort_if(!empty($contract->status) && $contract->status != '0' && $request->isSavingDraft, 400, 'You can not save draft for this contract');

    /*
    * if the contract is draft and now it is not draft then create event
    */
    if ($contract->status == '0' && !$request->isSavingDraft) {
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
      $data['status'] = '0';
    else {
      if ($contract->status == '0')
        $data['status'] = '1';
    }
    $contract->update($data + $request->validated());

    return $this->sendRes(__('Contract updated successfully'), ['event' => 'functionCall', 'function' => 'reloadDataTables']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Contract $contract)
  {
    $contract->delete();

    return $this->sendRes(__('Contract deleted successfully'), ['event' => 'functionCall', 'function' => 'reloadDataTables']);
  }

  public function releaseRetention(Contract $contract)
  {
    $contract->load('invoices');
    $contract->releaseInvoicesRetentions();

    return $this->sendRes(__('Retentions Released successfully'), ['event' => 'functionCall', 'function' => 'reloadDataTables']);
  }

  public function ContractPaymentsPlan(PaymentsPlanDataTable $dataTable)
  {
    $data['contract_statuses'] = ['' => 'All'] + Contract::STATUSES;
    $data['contractTypes'] = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');

    return $dataTable->render('admin.pages.contracts.paymentsplan.index', $data);
    // view('admin.pages.contracts.paymentsplan.index')
  }


  public function ContractPaymentsPlanReview(Contract $contract, ContractPaymentsPlanReviewDataTable $dataTable)
  {
        // Set the contract ID in the DataTable. This assumes that your DataTable
        // has a method to accept a contract object or ID.
        $dataTable->setContract($contract);

        // Generate and return the DataTable as a JSON response.
        // This will use the DataTable's internal mechanisms to create a JSON representation
        // of the data based on the current request (like pagination, filtering, etc.).
        return $dataTable->ajax();
  }

  public function ContractPaymentsPlanPhases($contract_id)
  {
    $query = ContractPhase::where('contract_phases.contract_id', $contract_id)->applyRequestFilters()
      ->with(['reviewdByAdmins', 'stage', 'contract:id,program_id,currency', 'addedAsInvoiceItem']);

      return DataTables::of($query)
          ->editColumn('checkbox', function ($phase) {
            return '<input class="form-check-input phase-check" name="selected_phases[]" type="checkbox" value="' . $phase->id . '">';
          })
          ->addColumn('stage_name', function ($phase) {
            return $phase->stage ? $phase->stage->name : 'N/A'; // Added a null check in case a phase doesn't have a related stage
          })
          ->editColumn('invoice_id', function ($phase) {
              $invoiceItem = $phase->addedAsInvoiceItem->first();
              return $invoiceItem
                  ? '<a href="' . route('admin.invoices.edit', $invoiceItem->invoice_id) . '">' . runtimeInvIdFormat($invoiceItem->invoice_id) . '</a>'
                  : 'N/A';
          })
          ->addColumn('can_reviewed_by', function ($phase) {
              return view('admin._partials.sections.user-avatar-group', ['users' => $phase->contract->canReviewedBy()->get()]);
          })
          ->addColumn('amount', function ($phase) {
              return view('admin.pages.contracts.paymentsplan.value-column', compact('phase'));
          })
          ->editColumn('actions', function ($phase) use ($contract_id) {
              $is_editable = !(@$phase->addedAsInvoiceItem[0]->invoice->status && in_array(@$phase->addedAsInvoiceItem[0]->invoice->status, ['Paid', 'Partial Paid']));
              return view('admin.pages.contracts.phases.actions', ['phase' => $phase, 'stage' => $phase->stage, 'contract_id' => $contract_id, 'is_editable' => $is_editable])->render();
          })
          ->addColumn('reviewed_by', function (ContractPhase $phase) {
            return view('admin._partials.sections.user-avatar-group', ['users' => $phase->reviewdByAdmins]);
          })
          ->addColumn('invoice_id', function ($phase) {
            $invoiceItem = $phase->addedAsInvoiceItem->first();
            return $invoiceItem
              ? '<a href="' . route('admin.invoices.edit', $invoiceItem->invoice_id) . '">' . runtimeInvIdFormat($invoiceItem->invoice_id) . '</a>'
              : 'N/A';
          })
          ->rawColumns(['actions', 'invoice_id', 'amount','reviewed_by', 'checkbox', 'invoice_id'])
          ->make(true);
  }

  public function ContractPaymentsPlanStages($contract_id)
  {
      $query = ContractStage::where('contract_id', $contract_id)
          ->withCount(['phases', 'myReviewedPhases'])
          ->with(['contract:id,program_id,currency', 'phases']);

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
          ->addColumn('can_reviewed_by', function (ContractStage $stage) {
              return view('admin._partials.sections.user-avatar-group', ['users' => $stage->contract->canReviewedBy()->get(), 'limit' => 5]);
          })
          ->addColumn('reviewed_by', function (ContractStage $stage) {
            return view('admin._partials.sections.user-avatar-group', ['users' => $stage->completelyReviewedBy()->get()]);
          })
          ->addColumn('my_review_progress', function (ContractStage $stage) {
            return view('admin._partials.sections.progressBar', ['perc' => $stage->getMyReviewProgress(), 'color' => 'primary', 'show_perc' => true, 'height' => '15px']);
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

          Admin::canReviewContract($contract, $contract->program_id)->findOrFail(auth()->id());

          // Check if the contract has already been reviewed by the current user
          $existingReview = $contract->reviews()->where('user_id', Auth::id())->first();

          if ($existingReview) {
              // The contract has already been reviewed by the current user.
              // Delete the review to mark the contract as "unreviewed"
              $existingReview->delete();
              return $this->sendRes('Contract marked as unreviewed!', ['event' => 'functionCall', 'function' => 'reloadDataTables', 'close' => 'globalModal', 'isReviewed' => false]);
          } else {
              // Mark the contract as reviewed
              $review = new Review([
                  'user_id' => Auth::id(),
                  'reviewed_at' => now(),
              ]);
              $contract->reviews()->save($review);
              return $this->sendRes('Contract marked as reviewed!', ['event' => 'functionCall', 'function' => 'reloadDataTables', 'close' => 'globalModal', 'isReviewed' => true]);
          }

      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
          return $this->sendError('Contract not found.');
      } catch (\Throwable $e) {
          // Log the error message for debugging
          // Log::error($e->getMessage());
          return $this->sendError($e->getMessage());
      }
  }


  public function togglePhaseReviewStatus($contract_id, $phase_id)
  {
      try {
          // Find the phase by its ID
          $phase = ContractPhase::where('contract_id', $contract_id)->with('contract.program:id')->findOrFail($phase_id);

          Admin::canReviewContract($contract_id, $phase->contract->program_id)->findOrFail(auth()->id());

          // Check if the phase has already been reviewed by the current user
          $existingReview = $phase->reviews()->where('user_id', Auth::id())->first();

          if ($existingReview) {
              // The phase has already been reviewed by the current user.
              // Delete the review to mark the phase as "unreviewed"
              $existingReview->delete();
              return $this->sendRes('Phase marked as unreviewed!', ['isReviewed' => false, 'close' => 'globalModal' ]);

             // return $this->sendRes('Phase marked as unreviewed!');
          } else {
              // Mark the phase as reviewed
              $review = new Review([
                  'user_id' => Auth::id(),
                  'reviewed_at' => now(),
              ]);
              $phase->reviews()->save($review);

              //return $this->sendRes('Phase marked as reviewed!');
              return $this->sendRes('Phase marked as reviewed!', ['isReviewed' => true, 'close' => 'globalModal' ]);
          }

      } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
          return $this->sendError('Contract not found.');
      } catch (Throwable $e) {
          return $this->sendError($e->getMessage());
      }
  }

  /**
   * update review status of multiple phases
   */
  public function updatePhasesReviewStatus(Contract $contract, Request $request) {
    $request->validate([
      'phase_ids' => 'required|array',
      'phase_ids.*' => 'required|integer|exists:contract_phases,id,contract_id,' . $contract->id,
      'is_reviewed' => 'required|boolean',
    ]);

    // fail if user is not allowed to review contract
    Admin::canReviewContract($contract->id, $contract->program_id)->findOrFail(auth()->id());

    DB::beginTransaction();

    try{
      if(!$request->boolean('is_reviewed')){
        $contract->phases()->whereIn('id', $request->phase_ids)->delete();
      }else{
        $phase_ids = filterInputIds($request->phase_ids);
        foreach ($phase_ids as $phase_id) {
          Review::updateOrCreate(
            ['user_id' => auth()->id(), 'reviewable_id' => $phase_id, 'reviewable_type' => ContractPhase::class],
            ['reviewed_at' => now()]
          );
        }
      }
      DB::commit();

      $message = $request->boolean('is_reviewed') ? __('Phases marked as reviewed!') : __('Phases marked as unreviewed!');

      return $this->sendRes($message, ['event' => 'functionCall', 'function' => 'reloadDataTables', 'close' => 'globalModal']);
    }catch(\Exception $e){
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function getContractsWithStagesAndPhases(Request $request)
  {
      $contracts = Contract::with(['stages.phases'])
      ->whereNull('deleted_at')
      ->get();

      $contractDataArray = $contracts->map(function ($contract) {
          return [
              'contract_name' => $contract->subject,
              'program' => $contract->program ? $contract->program->name : null,
              'assignable' => $contract->assignable->name, // Assuming a 'company' relationship exists
              'assignable_type' => $contract->assignable->type,
              'category' => $contract->category->name,
              'type' => $contract->type->name,
              'refrence_id' => $contract->refrence_id,
              'currency' => $contract->currency,
              'description' => $contract->description,
              'status' => $contract->status,
              'deleted_at' => $contract->deleted_at,
              'start_date' => $contract->start_date ? $contract->start_date->format('Y-m-d H:i:s') : null,
              'end_date' => $contract->end_date ? $contract->end_date->format('Y-m-d H:i:s') : null,
              'value' => $contract->value,
              'stages' => $contract->stages->map(function ($stage) {
                  return [
                      'name' => $stage->name,
                      'phases' => $stage->phases->map(function ($phase) {
                          return [
                          'name' => $phase->name,
                          'stage_id' => $phase->stage_id,
                          'name' => $phase->name,
                          'description' => $phase->description,
                          'estimated_cost' => $phase->estimated_cost,
                          'total_cost' => $phase->estimated_cost,
                          'start_date' => $phase->start_date ? $phase->start_date->format('Y-m-d\TH:i:s.u\Z') : null,
                          'due_date' => $phase->due_date ? $phase->due_date->format('Y-m-d\TH:i:s.u\Z') : null,
                        ];
                      })->toArray(),
                  ];
              })->toArray(),
          ];
      });

      return response()->json($contractDataArray);
  }

  public function getCompanies(Request $request)
  {
      $companies = Company::all()->map(function ($company) {
          return [
              'name' => $company->name,
              'type' => $company->type // assuming 'types' is a field or relation in your Company model
          ];
      });

      return response()->json($companies);
  }
}
