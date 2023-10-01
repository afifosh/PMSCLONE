<?php

namespace App\DataTables\Admin\Company;

use App\Models\ApprovalHistoryRequest;
use App\Models\CompanyApprovalRequest;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ApprovalHistoryRequestsDataTable extends DataTable
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
        return view('components.company.userAvatar', ['user' => $approvalRequest->sentBy]);
      })
      ->addColumn('pending', function ($row) {
        return view('admin._partials.sections.progressBar', ['perc' => $row->modificationsPendingApprovalPercentage(), 'color' => 'warning']);
      })
      ->addColumn('approved', function ($row) {
        return view('admin._partials.sections.progressBar', ['perc' => $row->modificationsApprovedPercentage(), 'color' => 'success']);
      })
      ->addColumn('rejected', function ($row) {
        return view('admin._partials.sections.progressBar', ['perc' => $row->modificationsRejectedPercentage(), 'color' => 'danger']);
      })
      ->editColumn('status', function ($approvalRequest) {
        return $this->makeApprovalStatus($approvalRequest->status);
      })
      ->editColumn('type', function ($row) {
        return $this->makeApprovalType($row->type);
      })
      ->addColumn('action', function ($approvalRequest) {
        return 'view';//view('pages.company-profile.approval-requests.action', compact('approvalRequest'));
      })
      ->rawColumns(['status', 'pending', 'approved', 'rejected', 'type']);
  }

  protected function makeApprovalStatus($status)
  {
    switch ($status) {
      case '1':
        return '<span class="badge bg-label-success">Approved</span>';
        break;
      case '0':
        return '<span class="badge bg-label-warning">Pending Approval</span>';
        break;
      case '3':
        return '<span class="badge bg-label-danger">Rejected</span>';
        break;

      default:
        return '<span class="badge bg-label-warning">Pending Approval</span>';
        break;
    }
  }

  public function makeApprovalType($type)
  {
    if ($type == 0) {
      return '<span class="badge bg-label-warning">Approval Request</span>';
    }
    return '<span class="badge bg-label-success">Modification Request</span>';
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\ApprovalHistoryRequest $model
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
      ->orderBy([0, 'DESC'])
      ->responsive(true)
      ->parameters([
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
      Column::make('sent_by'),
      Column::make('pending'),
      Column::make('approved'),
      Column::make('rejected'),
      Column::make('created_at')->title('Sent At'),
      Column::make('status'),
      Column::make('type'),
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
    return 'ApprovalHistoryRequests_' . date('YmdHis');
  }
}
