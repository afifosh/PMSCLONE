<?php

namespace App\DataTables\Admin;

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LocationsDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('owner', function ($location) {
        return $location->owner->name ?? 'N/A';
      })        
      ->addColumn('is_warehouse', function ($location) {
        if ($location->is_warehouse) {
            // Return the icon for true value
            return '<span class="badge bg-label-success">YES</span>'; // Replace with your desired icon
        } else {
            // Return something else or nothing for false value
            return '<span class="badge bg-label-danger">NO</span>'; 
        }
    })
      ->addColumn('action', function ($location) {
        return view('admin.pages.locations.action', compact('location'));
      })
      ->rawColumns(['is_warehouse']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Location $model): QueryBuilder
  {
    return $model->with('country', 'state', 'city', 'owner', 'addedBy')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Location</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add Location',
        'data-href' => route('admin.locations.create')
      ]
    ];
    return $this->builder()
      ->setTableId('locations-table')
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
      Column::make('address'),
      Column::make('owner'),
      Column::make('is_warehouse'),
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
    return 'Locations_' . date('YmdHis');
  }
}
