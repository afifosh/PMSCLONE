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
  /**
   * Build DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   * @return \Yajra\DataTables\EloquentDataTable
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('name', function ($row){
        return "<img class='avatar avatar-sm pull-up rounded-circle' src='$row->avatar' alt='Avatar'><span class='mx-2'><a href=".route('admin.companies.show', $row).">".$row->name."</a></span>";
        return "<img class='avatar avatar-sm pull-up rounded-circle' src='$row->avatar' alt='Avatar'><span class='mx-2'>".htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8')."</span>";
      })
      ->editColumn('added_by', function ($company) {
        return $company->addedBy->email ?? '-';
      })
      ->editColumn('status', function ($row) {
        return $this->makeStatus($row->status);
      })
      ->addColumn('action', function (Company $company) {
        return view('admin.pages.company.action', compact('company'));
      })
      ->filterColumn('added_by', function($query, $keyword){
        return $query->whereHas('addedBy', function($q) use ($keyword){
          return $q->where('email', 'like', '%'.$keyword.'%');
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
    return $model->with('addedBy');
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    if (auth('admin')->user()->can('create company'))
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create New Company</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Create New Company',
          'data-href' => route('admin.companies.create')
        ]
      ];
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
      ->addAction(['width' => '80px'])
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
      Column::make('email'),
      Column::make('added_by'),
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
