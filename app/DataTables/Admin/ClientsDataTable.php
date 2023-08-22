<?php

namespace App\DataTables\Admin;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ClientsDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('client', function ($row) {
        return view('admin._partials.sections.client-info', ['user' => $row]);
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
      })
      ->addColumn('action', function (Client $client) {
        return view('admin.pages.clients.action', compact('client'));
      })
      ->rawColumns(['user', 'action', 'status']);
  }

  protected function makeStatus($status)
  {
    $b_status = htmlspecialchars(ucwords($status), ENT_QUOTES, 'UTF-8');
    switch ($status) {
      case 'Active':
        return '<span class="badge bg-label-success">' . $b_status . '</span>';
        break;
      case 'Suspended':
        return '<span class="badge bg-label-secondary">' . $b_status . '</span>';
        break;

      default:
        return '<span class="badge bg-label-warning">' . $b_status . '</span>';
        break;
    }
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Client $model): QueryBuilder
  {
    return $model->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Client</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Add Client',
          'data-href' => route('admin.clients.create')
        ]
      ];

    return $this->builder()
      ->setTableId('clients-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
      <"row mx-2"<"col-md-2"<"me-3"l>>
      <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
      >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->orderBy(0, 'DESC')
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
      Column::make('client'),
      Column::make('phone'),
      Column::make('status'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Clients_' . date('YmdHis');
  }
}
