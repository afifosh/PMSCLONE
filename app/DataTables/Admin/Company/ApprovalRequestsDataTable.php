<?php

namespace App\DataTables\Admin\Company;

use App\Models\ApprovalLevel;
use App\Models\ApprovalRequest;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
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
      ->editColumn('name', function ($company) {
        $url = route('admin.approval-requests.level.companies.show', ['level' => $company->approval_level, 'company' => $company->id]);
        return view('admin._partials.sections.company-avatar', compact('company', 'url'));
      })
      ->editColumn('added_by', function ($company) {
        return $company->addedBy->email ?? '-';
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
      })
      ->filterColumn('added_by', function ($query, $keyword) {
        return $query->whereHas('addedBy', function ($q) use ($keyword) {
          return $q->where('email', 'like', '%' . $keyword . '%');
        });
      })
      ->setRowId('id')
      ->rawColumns(['name', 'action', 'status']);
  }

  protected function makeStatus($status)
  {
    $b_status = htmlspecialchars(ucwords($status), ENT_QUOTES, 'UTF-8');
    switch ($status) {
      case 'active':
        return '<span class="badge bg-label-success">' . $b_status . '</span>';
        break;
      case 'pending':
        return '<span class="badge bg-label-warning">' . $b_status . '</span>';
        break;
      case 'disabled':
        return '<span class="badge bg-label-secondary">' . $b_status . '</span>';
        break;

      default:
        return '<span class="badge bg-label-warning">' . $b_status . '</span>';
        break;
    }
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\Company $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(Company $model): QueryBuilder
  {
    $query = $model->newQuery();
    $query->whereIn('approval_status', [2, 3])->whereIn('approval_level', auth()->user()->approvalLevelsOrdered())->with('addedBy');
    return $query->applyRequestFilters();
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    return $this->builder()
      ->setTableId(Company::DT_ID)
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
      <"row mx-2"<"col-md-2"<"me-3"l>>
      <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
      >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->orderBy(0, 'DESC')
      ->parameters([
        'buttons' => $buttons,
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
      // Column::make('id'),
      Column::make('name')->title(__('Bussines Legal Name')),
      Column::make('website'),
      Column::make('source'),
      Column::make('added_by'),
      Column::make('approval_level'),
      Column::make('status'),
      Column::make('created_at'),
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
