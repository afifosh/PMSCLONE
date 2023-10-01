<?php

namespace App\DataTables\Admin\Project;

use App\Models\ProjectCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProjectCategoriesDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
    ->addColumn('action', function($category){
      return view('admin.pages.projects.categories.action', compact('category'));
    });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ProjectCategory $model): QueryBuilder
  {
    return $model->withCount('projects')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    if (auth('admin')->user()->can(true))
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Category</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' =>  [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Create New Category',
          'data-href' => route('admin.project-categories.create')
        ]
      ];
    return $this->builder()
      ->setTableId('project-categories-datatable')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->responsive(true)
      ->dom(
        '
          <"row mx-2"<"col-md-2"<"me-3"l>>
          <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
          >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->orderBy([0, 'DESC'])
      ->addAction(['width' => '80px', 'className' => 'text-center'])
      ->parameters([
        'buttons' => $buttons,
        "scrollX" => true,
        "drawCallback" => "function (settings) {
            $('[data-bs-toggle=\"tooltip\"]').tooltip();
          }"
      ]);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    return [
      Column::make('name')->title('Category Name'),
      Column::make('projects_count')->title('Projects'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'ProjectCategories_' . date('YmdHis');
  }
}
