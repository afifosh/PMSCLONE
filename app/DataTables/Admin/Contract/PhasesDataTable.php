<?php

namespace App\DataTables\Admin\Contract;

use App\Models\ContractPhase;
use App\Models\Phase;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PhasesDataTable extends DataTable
{
  public $contract;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
    ->editColumn('name', function ($phase) {
      return '<a href="' . route('admin.contracts.phases.milestones.index', [$this->contract, $phase]) . '">' . $phase->name . '</a>';
    })
    ->editColumn('change_request_id', function ($phase) {
      return $phase->change_request_id ? runtimeChangeReqIdFormat($phase->change_request_id) : '-';
    })
    ->rawColumns(['name']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ContractPhase $model): QueryBuilder
  {
    return $model->where('contract_id', $this->contract->id)->withCount('milestones')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('phases-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
        <"row mx-2"<"col-md-2"<"me-3"l>>
        <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
        >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      // ->addAction(['width' => '80px'])
      ->orderBy(0, 'DESC')
      ->responsive(true)
      ->parameters([
        'buttons' => [],
        "scrollX" => true
      ]);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    return [
      Column::make('name'),
      Column::make('milestones_count')->title('Milestones'),
      Column::make('change_request_id')->title('Change Request'),
      Column::make('type'),
      Column::make('status'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Phases_' . date('YmdHis');
  }
}
