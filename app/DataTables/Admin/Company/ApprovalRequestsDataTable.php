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
  public $type = 'approval';
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
        // $company->approval_level > 0 ?
        $url = route('admin.approval-requests.level.companies.show', ['level' => $company->approval_level, 'company' => $company->id]);// : null;
        // $url = $this->type == 'verified' ? null : $url;
        return view('admin._partials.sections.company-avatar', compact('company', 'url'));
      })
      ->editColumn('approval_status', function ($row) {
        return $this->makeApprovalStatus($row->approval_status);
      })
      ->addColumn('pending',function($com){
        return view('admin._partials.sections.progressBar', ['perc' => $com->profilePendingApprovalPercentage(), 'color' => 'warning']);
      })
      ->addColumn('approved', function ($com) {
        return view('admin._partials.sections.progressBar', ['perc' => $com->profileApprovedPercentage(), 'color' => 'success']);
      })
      ->addColumn('rejected', function ($com) {
        return view('admin._partials.sections.progressBar', ['perc' => $com->profileRejectedPercentage(), 'color' => 'danger']);
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
      })
      ->setRowId('id')
      ->rawColumns(['name', 'action', 'pending', 'approved', 'rejected', 'status', 'approval_status']);
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

  protected function makeApprovalStatus($status)
  {
    // $b_status = htmlspecialchars(ucwords($status), ENT_QUOTES, 'UTF-8');
    switch ($status) {
      case '1':
        return '<span class="badge bg-label-success">Approved</span>';
        break;
      case '2':
        return '<span class="badge bg-label-warning">Pending Approval</span>';
        break;
      case '3':
        return '<span class="badge bg-label-danger">Rejected</span>';
        break;

      default:
        return '<span class="badge bg-label-warning">Pending Info</span>';
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
    $query->when($this->type == 'approval', function ($q) {
      return $q->whereNull('approved_at')->whereIn('approval_status', [2, 3])->whereIn('approval_level', auth()->user()->approvalLevelsOrdered());
    });
    $query->when($this->type == 'change', function ($q) {
      return $q->whereNotNull('approved_at')->whereIn('approval_status', [3, 2])->whereIn('approval_level', auth()->user()->approvalLevelsOrdered());
    });
    $query->when($this->type == 'pending', function ($q) {
      return $q->whereNull('approved_at')->where('approval_level', 0);
    });
    $query->when($this->type == 'verified', function ($q) {
      return $q->whereNotNull('approved_at');
    });

    return $query->applyRequestFilters()->orderBy('verified_at', 'DESC');
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
      ->parameters(
        [
          'buttons' => $buttons,
          "scrollX" => true,
          "drawCallback" => "function (settings) {
            $('[data-bs-toggle=\"tooltip\"]').tooltip();
          }"
        ]
      );
  }

  /**
   * Get the dataTable columns definition.
   *
   * @return array
   */
  public function getColumns(): array
  {
    if ($this->type != 'verified') {
      return [
        // Column::make('id'),
        Column::make('name')->title(__('Bussines Legal Name')),
        Column::make('pending'),
        Column::make('approved'),
        Column::make('rejected'),
        Column::make('approval_level'),
        Column::make('approval_status'),
        Column::make('status'),
        Column::make('created_at'),
        Column::make('updated_at'),
      ];
    } else {
      return [
        Column::make('name')->title(__('Bussines Legal Name')),
        Column::make('verified_at'),
        Column::make('created_at'),
        Column::make('updated_at'),
      ];
    }
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
