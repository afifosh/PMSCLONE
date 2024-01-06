<?php

namespace App\DataTables\Admin;

use App\Models\Program;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProgramsDataTable extends DataTable
{
  /**
   * Build DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   * @return \Yajra\DataTables\EloquentDataTable
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('name', function ($row) {
        return view('admin._partials.sections.program-avatar', ['program' => $row]);
      })
      ->addColumn('parent', function (Program $program) {
        return @$program->parent->name ?? '-';
      })
      ->addColumn('children', function ($program) {
        return view('admin._partials.sections.programs-avatar-group', ['programs' => $program->children, 'limit' => 5]);
      })
      ->addColumn('contracts_count', function ($program) {
        return '<span class="badge badge-center rounded-pill bg-label-success">' . $program->contracts_count . '</span>';
      })
      ->addColumn('contracts_value', function ($program) {
        return view('admin.pages.programs.contracts-value', compact('program'));
      })
      ->orderColumn('contracts_count', function ($query, $order) {
        $query->orderByRaw("(select count(*) from `contracts` where `programs`.`id` = `contracts`.`program_id` and `contracts`.`deleted_at` is null) $order");
      })
      ->addColumn('action', function (Program $program) {
        return view('admin.pages.programs.action', compact('program'));
      })
      ->setRowId('id')
      ->rawColumns(['name', 'action', 'contracts_count', 'contracts_value']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\Program $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(Program $programs): QueryBuilder
  {
    return $programs->validAccessibleByAdmin(auth()->id())
      ->with('parent:id,name')
      ->with('children') // Eager load the children relationship
      ->with('contracts:id,program_id')
      ->leftjoin('contracts', 'programs.id', '=', 'contracts.program_id')
      // ->leftjoin('invoices', 'contracts.id', '=', 'invoices.contract_id')
      // Join only invoices that are not soft-deleted
      ->leftJoin('invoices', function ($join) {
        $join->on('contracts.id', '=', 'invoices.contract_id')
            ->whereNull('invoices.deleted_at'); // Exclude soft-deleted invoices
      })
      ->leftjoin('authority_invoices', 'invoices.id', '=', 'authority_invoices.invoice_id')
      ->select([
        'programs.id',
        'programs.name',
        'programs.name_ar',
        'programs.program_code',
        'programs.parent_id',
        'programs.updated_at',
        DB::raw('COUNT(contracts.id) as contracts_count'),
        DB::raw('SUM(invoices.total + authority_invoices.total)/100 as invoices_total'),
        DB::raw('SUM(invoices.paid_amount + authority_invoices.paid_wht_amount + authority_invoices.paid_rc_amount)/100 as invoices_paid_amount'),
        DB::raw('SUM(contracts.value)/100 as contracts_value'),
      ])
      ->groupBy([
        'programs.id',
        'programs.name',
        'programs.name_ar',
        'programs.program_code',
        'programs.parent_id',
        'programs.updated_at',
      ]);
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    if (auth('admin')->user()->can(true))
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Program</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Create New Program',
          'data-href' => route('admin.programs.create')
        ]
      ];
    return $this->builder()
      ->setTableId(Program::DT_ID)
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->responsive(true)
      ->dom(
        '
        <"row mx-2"<"col-md-2"<"me-3"l>>
        <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
        >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->orderBy([0, 'DESC'])
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
   *
   * @return array
   */
  public function getColumns(): array
  {
    return [
      // Column::make('id'),
      Column::make('name')->title('Program Name'),
      Column::make('parent'),
      Column::make('contracts_count')->title('Number of Contracts'),
      Column::make('program_code'),
      Column::make('children')->title('Child Programs'),
      Column::make('contracts_value')->title('Contracts Value'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get filename for export.
   *
   * @return string
   */
  protected function filename(): string
  {
    return 'Programs_' . date('YmdHis');
  }
}
