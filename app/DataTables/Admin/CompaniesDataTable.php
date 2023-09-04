<?php

namespace App\DataTables\Admin;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CompaniesDataTable extends DataTable
{
  public bool $approval_requests = false;
  /**
   * Build DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   * @return \Yajra\DataTables\EloquentDataTable
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('name', function ($company){
        return view('admin._partials.sections.company-avatar', compact('company'));
      })
      ->editColumn('added_by', function ($company) {
        return $company->addedBy ? view('admin._partials.sections.user-info', ['user' => $company->addedBy]) : '-';
      })
      ->editColumn('step_completed_count', function ($company) {
        $perc = ($company->step_completed_count/5)*100;
        return <<<EOL
                <div class="progress w-100" style="height:10px;">
                  <div class="progress-bar" role="progressbar" style="width: $perc%" aria-valuenow="$perc" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                EOL;
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
      })
      ->addColumn('action', function (Company $company) {
        return view('admin.pages.company.action', compact('company'));
      })
      ->filterColumn('added_by', function($query, $keyword){
        return $query->whereHas('addedBy', function($q) use ($keyword){
          return $q->where('email', 'like', '%'.$keyword.'%')
            ->orWhere('first_name', 'like', '%'.$keyword.'%')
            ->orWhere('last_name', 'like', '%'.$keyword.'%');
        });
      })
      ->setRowId('id')
      ->rawColumns(['name', 'action', 'status', 'step_completed_count']);
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
    $query->when($this->approval_requests, function ($query){
       $query->where('approval_status', 2);
    });
    return $query->with(['addedBy', 'addresses', 'bankAccounts', 'kycDocs', 'detail', 'contacts']);
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    if (auth('admin')->user()->can('create company') && !$this->approval_requests)
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Client</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Add Client',
          'data-href' => route('admin.companies.create')
        ]
      ];
    return $this->builder()
      ->setTableId(Company::DT_ID)
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
   *
   * @return array
   */
  public function getColumns(): array
  {
    return [
      // Column::make('id'),
      Column::make('name')->title(__('Bussines Legal Name')),
      Column::make('type'),
      Column::make('source'),
      Column::make('added_by'),
      // Column::make('step_completed_count')->title(__('Setup'))->orderable(false)->searchable(false),
      Column::make('status'),
      // Column::make('created_at'),
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
    return 'Companies_' . date('YmdHis');
  }
}
