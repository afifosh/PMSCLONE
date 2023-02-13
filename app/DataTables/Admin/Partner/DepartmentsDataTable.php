<?php

namespace App\DataTables\Admin\Partner;

use App\Models\CompanyDepartment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
// use Laravolt\Avatar\Avatar;
use Avatar;

class DepartmentsDataTable extends DataTable
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
      ->addColumn('head', function (CompanyDepartment $department) {
        if (!$department->head) {
          return '-';
        }
        return '<div class="d-flex justify-content-start align-items-center">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-3"><img src="' . $department->head->avatar . '" alt="Avatar" class="rounded-circle">
                  </div>
                </div>
                <div class="d-flex flex-column">
                  <span class="text-body text-truncate">
                    <span class="fw-semibold">' . htmlspecialchars($department->head->full_name, ENT_QUOTES, 'UTF-8') . '</span>
                  </span>
                  <small class="text-muted">' . htmlspecialchars($department->head->email, ENT_QUOTES, 'UTF-8') . '</small>
                </div>
              </div>';
      })
      ->addColumn('action', function (CompanyDepartment $department) {
        return view('admin.pages.partner.departments.action', compact('department'));
      })
      ->addColumn('organization', function (CompanyDepartment $department) {
        return $department->company->name;
      })
      ->filterColumn('head', function ($query, $keyword) {
        $query->whereHas('head', function ($q) use ($keyword) {
          $sql = "CONCAT(admins.first_name,' ',admins.last_name, ' ',admins.email)  like ?";
          return $q->whereRaw($sql, ["%{$keyword}%"]);
        });
      })
      ->filterColumn('organization', function ($query, $keyword) {
        $query->whereHas('company', function ($q) use ($keyword) {
          return $q->where('name', 'like', "%{$keyword}%");
        });
      })
      ->setRowId('id')
      ->rawColumns(['head', 'action']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\CompanyDepartment $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(CompanyDepartment $model): QueryBuilder
  {
    $query = $model->query();

    $query->when(request('filer_heads'), function ($q) {
      return $q->whereIn('head_id', request('filer_heads'));
    });

    $query->when(request('filter_organizations'), function ($q) {
      return $q->whereIn('company_id', request('filter_organizations'));
    });

    return $query;
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    if (auth('admin')->user()->can(true))
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New Department</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Add Department',
          'data-href' => route('admin.partner.departments.create')
        ]
      ];
    return $this->builder()
      ->setTableId(CompanyDepartment::DT_ID)
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
      Column::make('id'),
      Column::make('name'),
      Column::make('head'),
      Column::make('organization'),
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
    return 'Departments_' . date('YmdHis');
  }
}
