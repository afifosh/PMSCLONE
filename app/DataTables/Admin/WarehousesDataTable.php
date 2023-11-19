<?php

namespace App\DataTables\Admin;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WarehousesDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('owner', function ($warehouse) {
        return $warehouse->owner->name ?? 'N/A';
      })
      ->addColumn('country.name', function ($warehouse) {
        return $warehouse->location->country->name ?? 'N/A';
      })
      ->addColumn('state.name', function ($warehouse) {
        return $warehouse->location->state->name ?? 'N/A';
      })
      ->addColumn('city.name', function ($warehouse) {
        return $warehouse->location->city->name ?? 'N/A';
      })            
      ->addColumn('action', function ($warehouse) {
        return view('admin.pages.warehouses.action', compact('warehouse'));
      });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Warehouse $model): QueryBuilder
  {
    return $model->with('owner', 'addedBy')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Warehouse</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add Warehouse',
        'data-href' => route('admin.warehouses.create')
      ]
    ];
    return $this->builder()
      ->setTableId('warehouses-table')
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
      Column::make('name'),
      Column::make('owner'),
      // Column::make('address'),
      Column::make('country.name')->title('Country'),
      Column::make('state.name')->title('State'),
      Column::make('city.name')->title('City'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Warehouses_' . date('YmdHis');
  }
}
