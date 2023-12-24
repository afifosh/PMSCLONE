<?php

namespace App\DataTables\Admin\Applications;

use App\Models\Application;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ApplicationsDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('name', function ($application) {
        return view('admin.pages.applications.name', compact('application'));
      })
      ->addColumn('company', function ($application) {
        return $application->company ? view('admin._partials.sections.company-avatar', ['company' => $application->company]) : 'Public';
      })
      ->addColumn('program', function ($application) {
        return view('admin._partials.sections.program-avatar', ['program' => $application->program]);
      })
      ->editColumn('type_id', function ($application) {
        return $application->type->name;
      })
      ->editColumn('pipeline_id', function ($application) {
        return $application->pipeline->name;
      })
      ->addColumn('action', function (Application $application) {
        return view('admin.pages.applications.action', compact('application'));
      });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Application $model): QueryBuilder
  {
    return $model->mine()->with(['type', 'program', 'company', 'pipeline'])->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Application</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        // onclick => "window.location.href='" . route('admin.applications.create') . "'"
        'onclick' => "window.location.href='" . route('admin.applications.create') . "'",
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Application',
        'data-href' => route('admin.applications.create')
      ]
    ];

    return $this->builder()
      ->setTableId('applications-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
      <"row mx-2"<"col-md-2"<"me-3"l>>
      <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
      >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
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
      Column::make('name'),
      Column::make('company')->title('Company'),
      Column::make('program')->title('Program'),
      Column::make('type_id')->title('Type'),
      Column::make('pipeline_id')->title('Pipeline'),
      Column::make('start_at'),
      Column::make('end_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Applications_' . date('YmdHis');
  }
}
