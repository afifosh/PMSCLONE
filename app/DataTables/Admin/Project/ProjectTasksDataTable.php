<?php

namespace App\DataTables\Admin\Project;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProjectTasksDataTable extends DataTable
{
  public $project_id;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('checkbox', function ($task) {
        return '<input type="checkbox" class="form-check-input task-row-selector" value="' . $task->id . '" />';
      })
      ->editColumn('subject', function ($task) {
        return '<a href="javascript:;" data-href="' . route('admin.projects.tasks.show', [$task->project_id, $task->id]) . '" data-size="modal-xl" data-toggle="ajax-modal" data-title ="Task Details">' . $task->subject . '</a>';
      })
      ->editColumn('order', function () {
        return '<i class="fa-solid fa-bars cursor-move"></i>';
      })
      ->addColumn('checklist_items', function ($task) {
        return $task->checklistItems->whereNotNull('completed_by')->count() . '/' . $task->checklistItems->count();
      })
      ->editColumn('assignees', function ($task) {
        return view('admin._partials.sections.user-avatar-group', ['users' => $task->assignees, 'limit' => 3]);
      })
      ->addColumn('progress', function ($task) {
        return view('admin._partials.sections.progressBar', ['perc' => $task->progress_percentage(), 'color' => 'primary', 'show_perc' => true, 'height' => '14px']);
      })
      ->editColumn('status', function ($task) {
        return ucwords($task->status);
      })
      ->addColumn('action', function ($task) {
        return view('admin.pages.projects.tasks.action', compact('task'));
      })
      ->filterColumn('assignees', function ($query, $keyword) {
        $query->whereHas('assignees', function ($q) use ($keyword) {
          return $q->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%');
        });
      })
      ->setRowId('id')
      ->rawColumns(['checkbox', 'subject', 'order']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Task $model): QueryBuilder
  {
    return $model->where('project_id', $this->project_id)->with('assignees')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    if (auth('admin')->user()->can(true))
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Task</span>',
        'className' =>  'btn btn-primary ms-1',
        'attr' =>  [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Create New Task',
          'data-href' => route('admin.projects.tasks.create', $this->project_id)
        ]
      ];

    $buttons[] = [
      'text' => '<span class="d-none d-sm-inline-block">Sort</span>',
      'className' =>  'btn btn-primary',
      'attr' =>  [
        'onClick' => "toggleSortColumn();"
      ]
    ];

    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Template</span>',
      'className' =>  'btn btn-primary',
      'attr' =>  [
        'onClick' => "showSelectColumn(this);"
      ]
    ];
    $buttons[] = [
      'text' => '<span class="d-none d-sm-inline-block">Save Template</span>',
      'className' =>  'btn btn-primary d-none saveTemplateButton',
      'attr' =>  [
        'onClick' => 'showSaveTemplateModal();'
      ]
    ];

    $buttons[] = [
      'text' => '<span class="d-none d-sm-inline-block">Import Template</span>',
      'className' =>  'btn btn-primary',
      'attr' =>  [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Import Template',
        'data-href' => route('admin.projects.import-templates.create', ['project' => $this->project_id])
      ]
    ];

    return $this->builder()
      ->setTableId('project-tasks-datatable')
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
          view_task_from_url();
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
      Column::make('checkbox')->hidden()->title('<input type="checkbox" class="form-check-input select-all-tasks"/>')->orderable(false)->searchable(false)->printable(false)->exportable(false)->width('10px'),
      Column::make('order')->hidden()->title('sort')->id('sort_column')->searchable(false)->printable(false)->exportable(false),
      Column::make('subject')->title('Task Name'),
      Column::make('status'),
      Column::make('due_date'),
      Column::make('assignees')->orderable(false),
      Column::make('checklist_items')->title('Checklist')->orderable(false)->searchable(false)->printable(false)->exportable(false),
      Column::make('progress')->searchable(false)->orderable(false),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'ProjectTasks_' . date('YmdHis');
  }
}
