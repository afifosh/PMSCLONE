<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Program;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TrackingPaymentsPlanDataTable extends DataTable
{
  public $projectId = null;
  public $company = null;
  public $program = null;
  public $programId = null;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))

      ->addColumn('can_review', function (Contract $contract) {
        return view('admin._partials.sections.user-avatar-group', ['users' => $contract->canReviewedBy()->get(), 'limit' => 5]);
          })
      ->addColumn('reviews_completed', function (Contract $contract) {//usersWhoCompletedAllPhases();
        return view('admin._partials.sections.user-avatar-group', ['users' => $contract->usersCompletedPhasesReview()->get(), 'limit' => 5]);
      })
      ->addColumn('my_review_progress', function ($contract) {
        if($contract->phases_count)
          return view('admin._partials.sections.progressBar', ['perc' => $contract->myPhasesReviewProgress(), 'color' => 'primary', 'show_perc' => true, 'height' => '15px']);
        else
          return 'N/A';
      })
      ->editColumn('program.name', function ($contract) {
        return $contract->program_id
            ? '<a href="' . route('admin.programs.show', $contract->program->id) . '">' . $contract->program->name . '</a>'
            : 'N/A';
      })
      ->addColumn('expand', function ($contract) {
        return '<i class="ti ti-layout-sidebar-left-expand ti-md me-3 btn-expand" contract-id="' . $contract->id . '"></i>';
      })
      ->editColumn('subject', function ($contract) {
        return $contract->subject
            ? '<a href="' . route('admin.contracts.show', $contract->id) . '">' . e($contract->subject) . '</a>'
            : view('admin.pages.contracts.name', ['contract_id' => $contract->id]);
    })
      ->addColumn('action', function ($contract) {
        return view('admin.pages.contracts.action', compact('contract'));
      })
      ->editColumn('stages_count', function ($contract) {
        return $contract->stages_count ? $contract->stages_count : '0';
      })
      ->editColumn('phases_count', function ($contract) {
        return $contract->phases_count ? $contract->phases_count : '0';
      })
      ->addColumn('assigned_to', function ($project) {
        if ($project->assignable instanceof Company) {
          return view('admin._partials.sections.company-avatar', ['company' => $project->assignable]);
        }

        return '-';
      })
      // ->editColumn('project.name', function ($project) {
      //   return $project->project ? $project->project->name : '-';
      // })

      ->editColumn('value', function (Contract $contract) {
        return @cMoney($contract->value ?? 0, $contract->currency, true);
        //return view('admin.pages.contracts.value-column', compact('contract'));
      })
      ->filterColumn('assigned_to', function ($query, $keyword) {
        $query->whereHasMorph('assignable', Company::class, function ($q) use ($keyword) {
          $q->where('name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
        });
      })
      ->rawColumns(['id', 'program.name','subject','reviews_completed','expand','incomplete_reviewers']);
  }

/**
 * Get the query source of dataTable.
 */
public function query(Contract $model): QueryBuilder
{
    $query = $model->validAccessibleByAdmin(auth()->id())->newQuery()
        ->select([
            'contracts.id',
            'contracts.program_id',
            'contracts.refrence_id',
            'contracts.subject',
            'contracts.project_id',
            'contracts.type_id',
            'contracts.category_id',
            'contracts.value',
            'contracts.start_date',
            'contracts.end_date',
            'contracts.status',
            'contracts.assignable_id',
            'assignable_type',
            DB::raw('(SELECT COUNT(DISTINCT contract_stages.id) FROM contract_stages WHERE contract_id = contracts.id) as stages_count'),
            DB::raw('(SELECT COUNT(DISTINCT contract_phases.id) FROM contract_phases
                     JOIN contract_stages ON contract_stages.id = contract_phases.stage_id
                     WHERE contract_stages.contract_id = contracts.id) as phases_count'),
            DB::raw('COALESCE(SUM(invoices.total), 0)/100 as total'),
            DB::raw('COALESCE(SUM(invoices.paid_amount), 0)/100 as paid_amount'),
            DB::raw('COALESCE(sum(invoices.total - invoices.paid_amount), 0)/100 as due_amount'),
            DB::raw('COALESCE(sum(invoices.total_tax), 0)/100 as total_tax'),
            DB::raw('CASE WHEN SUM(contracts.value) = 0 THEN 0 ELSE COALESCE((sum(invoices.paid_amount)/sum(contracts.value)), 0) * 100 END as paid_percent'),
            DB::raw('count(CASE WHEN invoices.retention_amount IS NOT NULL AND invoices.retention_released_at IS NULL THEN 1 ELSE NULL END) as pending_retentions_count')
        ])
        ->leftJoin('invoices', 'contracts.id', '=', 'invoices.contract_id')
        ->with(['type', 'assignable.detail', 'category']);

    if ($this->projectId) {
        $query->where('project_id', $this->projectId);
    }

    if($this->company){
      $query->where('assignable_id', $this->company->id)
            ->where('assignable_type', Company::class);
    }

    if ($this->program) {
      $childProgramIds = Program::where('parent_id', $this->program->id)->pluck('id')->toArray();
      $programIds = array_merge([$this->program->id], $childProgramIds);
      $query->whereIn('program_id', $programIds);
    }

    $query->applyRequestFilters();

    return $query->groupBy([
        'contracts.id',
        'contracts.program_id',
        'contracts.refrence_id',
        'contracts.subject',
        'contracts.project_id',
        'contracts.type_id',
        'contracts.category_id',
        'contracts.value',
        'contracts.start_date',
        'contracts.end_date',
        'contracts.status',
        'contracts.assignable_id',
        'assignable_type'
    ]);
}



  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {

    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Contract</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Contract',
        'data-href' => route('admin.contracts.create', ['project' => $this->projectId])
      ]
    ];

    return $this->builder()
      ->setTableId('paymentsplan-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
        <"row mx-2"<"col-md-2"<"me-3"l>>
        <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
        >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->orderBy([0, 'desc'])
      ->responsive(true)
      ->parameters([
        'buttons' => $buttons,
        "scrollX" => true,
        "drawCallback" => "function (settings) {
          $('[data-bs-toggle=\"tooltip\"]').tooltip();

          var api = this.api();
          var dataLists = api.data();
          if (dataLists.length === 1) {
              var contractId = dataLists[0].id;

              var expandButton = $('[contract-id=\"' + contractId + '\"]');
              if (expandButton.length > 0) {
                  expandButton.trigger('click');
              }
          }

      }"
  ]);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    return [
      Column::computed('expand')
      ->exportable(false)
      ->printable(false)
      ->width(60)
      ->addClass('text-center')
      ->title(''), // This is an empty title for the expand/collapse column
      Column::make('subject')->title('Contract Name'),
      Column::make('program.name')->name('programs.name')->title('Program'),
      Column::make('stages_count')->title('Payments Plans')->searchable(false),
      Column::make('phases_count')->title('Payments Terms')->searchable(false),
      Column::make('value')->title('Amount'),
      Column::make('can_review')->title('Can Review'),
      Column::make('reviews_completed')->title('Reviews Completed'),
      Column::make('my_review_progress')->title('My Progress'),
      Column::make('status'),
    ];
  }

  public function setProgram($programId)
  {
      $this->$programId = $programId;
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Contracts_' . date('YmdHis');
  }
}
