<?php

namespace App\DataTables\Admin\Contract;

use App\Models\ContractChangeRequest;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ChangeRequestsDataTable extends DataTable
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
    ->addColumn('sender', function($change_order) {
      if(!$change_order->sender) return '-';
      if($change_order->sender_type == 'App\Models\Admin') {
        return view('admin._partials.sections.user-info', ['user' => $change_order->sender])->render();
      } else {
        return $change_order->sender->full_name;
      }
    })
    ->editColumn('old_value', function($change_order) {
      return $change_order->pritableOldValue();
    })
    ->editColumn('new_value', function($change_order) {
      return $change_order->pritableNewValue();
    })
    ->editColumn('action', function($change_request){
      return view('admin.pages.contracts.change-requests.action', compact('change_request'));
    })
    ->rawColumns(['sender', 'action']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ContractChangeRequest $model): QueryBuilder
  {
    return $model->where('contract_id', $this->contract->id)->with('sender')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Request</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Change Request',
        'data-href' => route('admin.contracts.change-requests.create', ['contract' => $this->contract->id])
      ]
    ];

    return $this->builder()
      ->setTableId('change-requests-table')
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
      Column::make('sender'),
      Column::make('old_value')->title('Old Amount'),
      Column::make('new_value')->title('New Amount'),
      Column::make('old_end_date')->title('Old End Date'),
      Column::make('new_end_date')->title('New End Date'),
      Column::make('created_at')->title('Ordered At'),
      Column::make('status')->title('Status'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'ChangeRequests_' . date('YmdHis');
  }
}
