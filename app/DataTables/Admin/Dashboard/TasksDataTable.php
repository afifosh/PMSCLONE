<?php

namespace App\DataTables\Admin\Dashboard;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TasksDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
    ->editColumn('subject', function($task){
      return '<a href="javascript:;" data-href="'.route('admin.projects.tasks.show', [$task->project_id, $task->id]).'" data-size="modal-xl" data-toggle="ajax-modal" data-title ="Task Details">'.$task->subject.'</a>';
    })
    ->editColumn('assignees', function($task) {
      return view('admin._partials.sections.user-avatar-group', ['users' => $task->assignees, 'limit' => 3]);
    })
    ->addColumn('progress', function($task){
      return view('admin._partials.sections.progressBar', ['perc' => $task->progress_percentage(), 'color' => 'primary', 'show_perc' => true, 'height' => '14px']);
    })
    ->editColumn('project.name', function($task){
      return '<a href="'.route('admin.projects.show', $task->project_id).'">'.$task->project->name.'</a>';
    })
    ->editColumn('status', function($task){
      return ucwords($task->status);
    })
    ->addColumn('action', function($task){
      return view('admin.pages.projects.tasks.action', compact('task'));
    })
    ->filterColumn('assignees', function ($query, $keyword) {
      $query->whereHas('assignees', function ($q) use ($keyword) {
        return $q->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%');
      });
    })
    ->rawColumns(['subject', 'project.name']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Task $model): QueryBuilder
  {
    $q = $model->whereHas('assignees', function($q){
      $q->where('admin_id', auth()->id());
    })->with('assignees', 'project', 'checklistItems')->newQuery();

    return $q->applyRequestFilters();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    return $this->builder()
      ->setTableId('tasks-datatable')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->responsive(true)
      // ->addAction(['width' => '80px', 'className' => 'text-center'])
      ->dom(
        '
          <"row mx-2"<"col-md-2"<"me-3"l>>
          <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
          >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->orderBy([0, 'DESC'])
      ->parameters([
        'buttons' => $buttons,
        "scrollX" => true,
        "drawCallback" => "function (settings) {
            $('[data-bs-toggle=\"tooltip\"]').tooltip();
          }",
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
      Column::make('id'),
      Column::make('subject')->title('Task Name'),
      Column::make('project.name')->title('Project Name'),
      Column::make('status'),
      Column::make('start_date'),
      // Column::make('due_date'),
      // Column::make('assignees')->orderable(false),
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
