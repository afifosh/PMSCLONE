<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Company;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PaymentsPlanDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))

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
      ->editColumn('value', function (Contract $contract) {
        return @cMoney($contract->value ?? 0, $contract->currency, true);
        //return view('admin.pages.contracts.value-column', compact('contract'));
      })
      ->addColumn('can_review', function ($contract) {
        return view('admin._partials.sections.user-avatar-group', ['users' => $contract->canReviewedBy()->get(), 'limit' => 5]);
      })
      ->addColumn('reviewedBy', function ($contract) {
        if ($contract->phases_count)
          return view('admin._partials.sections.user-avatar-group', ['users' => $contract->usersCompletedPhasesReview()->get(), 'limit' => 5]);
        else
          return 'N/A';
      })
      ->addColumn('my_review_progress', function ($contract) {
        if ($contract->phases_count)
          return view('admin._partials.sections.progressBar', ['perc' => $contract->myPhasesReviewProgress(), 'color' => 'primary', 'show_perc' => true, 'height' => '15px']);
        else
          return 'N/A';
      })
      ->filterColumn('assigned_to', function ($query, $keyword) {
        $query->whereHasMorph('assignable', Company::class, function ($q) use ($keyword) {
          $q->where('name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
        });
      })
      ->rawColumns(['id', 'program.name', 'subject', 'expand']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Contract $model): QueryBuilder
  {
    return $model->validAccessibleByAdmin(auth()->id())->applyRequestFilters()->with(['program:id,name,name_ar'])->withCount(['stages', 'phases', 'myReviewedPhases']);
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
        'drawCallback' => 'function(){
            expandOldExpandedRow();
            $(\'[data-bs-toggle="tooltip"]\').tooltip();
          }'
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
      Column::make('stages_count')->title('Payments Plans')->searchable(false),
      Column::make('phases_count')->title('Payments Terms')->searchable(false),
      Column::make('value')->title('Amount'),
      // Column::make('paid_percent')->title('Paid')->searchable(false),
      // Column::make('phases_count')->title('Phases')->searchable(false),
      Column::make('can_review')->title('Can Review'),
      Column::make('reviewedBy')->title('Reviewed By'),
      Column::make('my_review_progress')->title('Progress'),
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
