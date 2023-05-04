<?php

namespace App\DataTables\Company\Profile;

use App\Models\CompanyApprovalRequest;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ApprovalRequestsDataTable extends DataTable
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
      ->editColumn('sent_by', function ($approvalRequest) {
        return $approvalRequest->sentBy->full_name;
      })
      ->addColumn('action', function ($approvalRequest) {
        return view('pages.company-profile.approval-requests.action', compact('approvalRequest'));
      });
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\ApprovalRequest $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(CompanyApprovalRequest $model): QueryBuilder
  {
    return $model->newQuery();
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('approvalrequests-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->addAction(['width' => '80px'])
      ->orderBy(0, 'DESC')
      ->parameters([
        "scrollX" => true
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
      Column::make('sent_by'),
      Column::make('created_at')->title('Sent At'),
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
    return 'ApprovalRequests_' . date('YmdHis');
  }
}
