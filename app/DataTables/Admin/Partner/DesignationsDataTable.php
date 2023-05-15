<?php

namespace App\DataTables\Admin\Partner;

use App\Models\CompanyDesignation;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DesignationsDataTable extends DataTable
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
      ->addColumn('action', function (CompanyDesignation $designation) {
        return view('admin.pages.partner.designations.action', compact('designation'));
      })
      ->addColumn('department', function (CompanyDesignation $designation) {
        return $designation->department->name;
      })
      ->addColumn('organization', function (CompanyDesignation $designation) {
        return @$designation->department->company->name ? view('admin._partials.sections.company-avatar', ['company' => $designation->department->company]) : '-';
      })
      ->filterColumn('organization', function ($query, $keyword) {
        $query->whereHas('department.company', function ($q) use ($keyword) {
          return $q->where('name', 'like', "%{$keyword}%");
        });
      })
      ->filterColumn('department', function ($query, $keyword) {
        $query->whereHas('department', function ($q) use ($keyword) {
          return $q->where('name', 'like', "%{$keyword}%");
        });
      })
      ->setRowId('id')
      ->rawColumns(['action']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\CompanyDesignation $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(CompanyDesignation $model): QueryBuilder
  {
    $query = $model->query();

    $query->when(request('filer_departments'), function ($q) {
      return $q->whereIn('department_id', request('filer_departments'));
    });

    $query->when(request('filter_organizations'), function ($q) {
      $q->whereHas('department', function ($dep) {
        return $dep->whereIn('company_id', request('filter_organizations'));
      });
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
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New Designation</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Add Designation',
          'data-href' => route('admin.partner.designations.create')
        ]
      ];
    return $this->builder()
      ->setTableId(CompanyDesignation::DT_ID)
      ->columns($this->getColumns())
      ->responsive(true)
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
      Column::make('name')->title('Designation Name'),
      Column::make('department'),
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
    return 'Designations_' . date('YmdHis');
  }
}
