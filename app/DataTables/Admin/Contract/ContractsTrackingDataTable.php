<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Company;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContractsTrackingDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('contracts.id', function ($contract) {
        return view('admin.pages.contracts.name', ['contract_id' => $contract->id]);
      })
      ->editColumn('subject', function ($contract) {
        return $contract->subject ? $contract->subject : '-';
      })
      ->editColumn('program.name', function ($contract) {
        return $contract->program_id
          ? '<a href="' . route('admin.programs.show', $contract->program->id) . '">' . $contract->program->name . '</a>'
          : 'N/A';
      })
      ->addColumn('action', function ($contract) {
        return view('admin.pages.contracts.action', compact('contract'));
      })
      ->addColumn('reviewers', function ($contract) {
        return view('admin._partials.sections.user-avatar-group', ['users' => $contract->canReviewedBy()->get(), 'limit' => 5]);
      })
      ->addColumn('reviews_completed', function ($contract) {
        return view('admin._partials.sections.user-avatar-group', ['users' => $contract->reviewedBy, 'limit' => 5]);
      })
      ->addColumn('assigned_to', function ($project) {
        if ($project->assignable instanceof Company) {
          return view('admin._partials.sections.company-avatar', ['company' => $project->assignable]);
        } else {
          return '-';
        }
      })
      // ->editColumn('project.name', function ($project) {
      //   return $project->project ? $project->project->name : '-';
      // })
      ->editColumn('type.name', function ($project) {
        return $project->type ? $project->type->name : '-';
      })
      ->editColumn('category.name', function ($project) {
        return $project->category ? $project->category->name : '-';
      })
      ->editColumn('value', function (Contract $contract) {
        return @cMoney($contract->value ?? 0, $contract->currency, true);
        // return view('admin.pages.contracts.value-column', compact('contract'));
      })
      ->filterColumn('assigned_to', function ($query, $keyword) {
        $query->whereHasMorph('assignable', Company::class, function ($q) use ($keyword) {
          $q->where('name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
        });
      })
      ->rawColumns(['id', 'program.name', 'reviews_completed']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Contract $model): QueryBuilder
  {
    return $model->validAccessibleByAdmin(auth()->id())->applyRequestFilters();
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
        "scrollX" => true,
        "drawCallback" => "function (settings) {
            $('[data-bs-toggle=\"tooltip\"]').tooltip();
          }"
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
      // Column::make('program.name')->name('programs.name')->title('Program'),
      // Column::make('refrence_id')->title('Ref ID'),
      // Column::make('assigned_to')->title('Assigned To'),
      Column::make('value')->title('Amount'),
      // Column::make('paid_percent')->title('Paid')->searchable(false),
      Column::make('reviewers')->title('Reviewers'),
      Column::make('reviews_completed')->title('Reviews Completed'),
      Column::make('start_date'),
      Column::make('end_date'),
      // Column::make('phases_count')->title('Phases')->searchable(false),
      Column::make('status'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Contracts_' . date('YmdHis');
  }
}
