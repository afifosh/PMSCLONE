<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Program;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContractsDataTable extends DataTable
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
      ->editColumn('subject', function ($contract) {
        return view('admin.pages.contracts.subject', compact('contract'));
      })
      // ->editColumn('subject', function ($contract) {
      //   return $contract->subject ? $contract->subject : '-';
      // })
      ->editColumn('program.name', function ($contract) {
        return $contract->program_id
            ? '<a href="' . route('admin.programs.show', $contract->program->id) . '">' . $contract->program->name . '</a>'
            : 'N/A';
      })
      ->addColumn('action', function ($contract) {
        return view('admin.pages.contracts.action', compact('contract'));
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
        return view('admin.pages.contracts.value-column', compact('contract'));
      })
      ->editColumn('start_date', function ($contract) {
        return $contract->start_date ? $contract->start_date->format('d M, Y') : '-';
      })
      ->editColumn('end_date', function ($contract) {
        return $contract->end_date ? $contract->end_date->format('d M, Y') : '-';
      })
      ->editColumn('status', function ($contract) {
        return view('admin.pages.contracts.status', ['status' => $contract->status]);
      })
      ->filterColumn('assigned_to', function ($query, $keyword) {
        $query->whereHasMorph('assignable', Company::class, function ($q) use ($keyword) {
          $q->where('name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
        });
      })
      ->rawColumns(['subject', 'program.name', 'status']);
  }

/**
 * Get the query source of dataTable.
 */
public function query(Contract $model): QueryBuilder
{
    // Start the base query
    $query = $model->validAccessibleByAdmin(auth()->id())->newQuery()
        ->select([
            'contracts.id',
            'contracts.program_id',
            'contracts.refrence_id',
            'contracts.subject', // Add this line for the subject column
            'contracts.subject_ar',
            'contracts.project_id',
            'contracts.type_id',
            'contracts.category_id',
            'contracts.value',
            'contracts.start_date',
            'contracts.end_date',
            'contracts.status',
            'contracts.assignable_id',
            'assignable_type',
            DB::raw('SUM(invoices.total)/1000 as total'),
            DB::raw('SUM(invoices.paid_amount)/1000 as paid_amount'),
            DB::raw('sum(invoices.total - invoices.paid_amount)/1000 as due_amount'),
            DB::raw('sum(invoices.total_tax)/1000 as total_tax'),
            DB::raw('(sum(invoices.paid_amount)/sum(contracts.value))*1000 as paid_percent'),
            DB::raw('SUM(authority_invoices.total_wht)/1000 as total_wht'),
            DB::raw('SUM(authority_invoices.paid_wht_amount)/1000 as paid_wht_amount'),
            DB::raw('SUM(authority_invoices.total_rc)/1000 as total_rc'),
            DB::raw('SUM(authority_invoices.paid_rc_amount)/1000 as paid_rc_amount'),
            // contract invoice count whose retention_released_at is null as pending_retentions_count
            DB::raw('sum(CASE WHEN invoices.retention_amount IS NOT NULL AND invoices.retention_released_at IS NULL THEN invoices.retention_amount ELSE 0 END) as pending_retentions_sum'),

            DB::raw('count(CASE WHEN invoices.retention_amount IS NOT NULL AND invoices.retention_released_at IS NULL THEN 1 ELSE NULL END) as pending_retentions_count')
        ])
        ->leftJoin('invoices', 'contracts.id', '=', 'invoices.contract_id')
        ->leftJoin('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        ->leftJoin('programs', 'contracts.program_id', '=', 'programs.id')
        ->leftJoin('authority_invoices', 'invoices.id', '=', 'authority_invoices.invoice_id')
        ->groupBy([
            'contracts.id',
            'contracts.program_id',
            'contracts.refrence_id',
            'contracts.subject', // Add this line for the subject column
            'contracts.subject_ar',
            'contracts.project_id',
            'contracts.type_id',
            'contracts.category_id',
            'contracts.value',
            'contracts.start_date',
            'contracts.end_date',
            'contracts.status',
            'contracts.assignable_id',
            'assignable_type'
        ])
        ->with(['assignable.detail']);

    // If a projectId is provided, filter by it
    if ($this->projectId) {
        $query->where('project_id', $this->projectId);
    }
    if($this->company){
      $query->where('assignable_id', $this->company->id)->where('assignable_type', Company::class);
    }
    // If a program is provided, filter by it and its children
    // if ($this->program) {
    //   // Fetch IDs for all children of the given program
    //   $childProgramIds = Program::where('parent_id', $this->program->id)->pluck('id')->toArray();

    //   // Include the main program's ID
    //   $programIds = array_merge([$this->program->id], $childProgramIds);

    //   // Use these IDs to filter contracts
    //   $query->whereIn('program_id', $programIds);
    // }

    // Apply any additional filters if necessary
    $query->with(['program', 'reviewedBy'])->applyRequestFilters();

    return $query;
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
      ->setTableId('contracts-table')
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
        "scrollX" => true
      ]);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    return [
      // Column::make('contracts.id')->title('Contract'),
      Column::make('subject')->title('Subject'),
      Column::make('program.name')->name('programs.name')->title('Program'),
      Column::make('refrence_id')->title('Ref ID'),
      Column::make('assigned_to')->title('Assigned To'),
      Column::make('value')->title('Amount'),
      // Column::make('paid_percent')->title('Paid')->searchable(false),
      Column::make('start_date'),
      Column::make('end_date'),
      // Column::make('phases_count')->title('Phases')->searchable(false),
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
