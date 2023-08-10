<?php

namespace App\DataTables\Admin\Project;

use App\Models\ProjectTemplate;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TemplatesDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->addColumn('tasks_count', function($template){
          return $template->taskTemplates()->count();
        })
        ->editColumn('name', function($template){
          return view('admin.pages.projects.templates.template-name', compact('template'))->render();
        })
        ->addColumn('action', function($template){
          return view('admin.pages.projects.templates.action', compact('template'))->render();
        })
        ->rawColumns(['name', 'action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ProjectTemplate $model): QueryBuilder
    {
        return $model->where('admin_id', auth()->id())->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
      $buttons = [];

      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Template</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Create New Template',
          'data-href' => route('admin.project-templates.create')
        ]
      ];

      return $this->builder()
        ->setTableId('project-templates-datatable')
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->responsive(true)
        ->addAction(['width' => '80px', 'className' => 'text-center'])
        ->dom(
          '
            <"row mx-2"<"col-md-2"<"me-3"l>>
            <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
            >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        )
        ->orderBy(1, 'asc')
        ->parameters([
          'buttons' => $buttons,
          "scrollX" => true,
          'drawCallback' => "function(){
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
          Column::make('name')->title('Name'),
          Column::make('tasks_count')->title('No. of Tasks'),
          Column::make('created_at')->title('Created At'),
          Column::make('updated_at')->title('Updated At'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Templates_' . date('YmdHis');
    }
}
