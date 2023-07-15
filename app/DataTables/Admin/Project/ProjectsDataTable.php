<?php

namespace App\DataTables\Admin\Project;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProjectsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->editColumn('name', function($project){
          return '<a href="'.route('admin.projects.show', $project->id).'">'.$project->name.'</a>';
        })
        ->editColumn('status', function ($project) {
          return '<span class="badge bg-label-'.$this->resolveStatus($project->status)['color'].'" style="width:92px">'.$this->resolveStatus($project->status)['status'].'</span>';
        })
        ->editColumn('members', function($project) {
          return view('admin._partials.sections.user-avatar-group', ['users' => $project->members, 'limit' => 3]);
        })
        ->addColumn('progress', function($proj){
          $progress = $proj->tasks->count() != 0 ? $proj->tasks->where('status', 'Completed')->count() / $proj->tasks->count() * 100 : 0;
          return view('admin._partials.sections.progressBar', ['perc' => $progress, 'color' => 'primary']);
        })
        ->filterColumn('members', function ($query, $keyword) {
          $query->whereHas('members', function ($q) use ($keyword) {
            return $q->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%');
          });
        })
        ->filterColumn('category', function ($query, $keyword) {
          $query->whereHas('category', function ($q) use ($keyword) {
            return $q->where('name', 'like', '%' . $keyword . '%');
          });
        })
        ->rawColumns(['status', 'name']);
    }

    protected function resolveStatus($status)
    {
      switch ($status) {
        case '0':
          return ['color' => 'warning', 'status' => 'Not Started'];
          break;
        case '1':
          return ['color' => 'success', 'status' => 'In Progress'];
          break;
        case '2':
          return ['color' => 'danger', 'status' => 'On Hold'];
          break;
        case '3':
          return ['color' => 'danger', 'status' => 'Cancelled'];
          break;
        case '4':
          return ['color' => 'success', 'status' => 'Completed'];
          break;
        default:
          return ['color' => 'danger', 'status' => 'Unknown'];
          break;
      }
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Project $model): QueryBuilder
    {
        return $model->with('program', 'members', 'category')->mine()->select('projects.*')->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
      $buttons = [];
      if (auth('admin')->user()->can(true))
        $buttons[] = [
          'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Project</span>',
          'className' =>  'btn btn-primary mx-3',
          'action' => 'function (e, dt, node, config) {
            window.location = "'.route('admin.projects.create').'";
          }',
        ];
      return $this->builder()
        ->setTableId('projects-table')
        ->columns($this->getColumns())
        ->minifiedAjax()
        ->responsive(true)
        ->dom(
          '
          <"row mx-2"<"col-md-2"<"me-3"l>>
          <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
          >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        )
        ->orderBy(0, 'desc')
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
            Column::make('id')->title('ID'),
            Column::make('name')->title('Project Name'),
            Column::make('program.name')->title('Program Name'),
            // Column::make('category.name')->title('Category'),
            Column::make('start_date'),
            Column::make('deadline'),
            Column::make('members'),
            Column::make('progress')->searchable(false)->orderable(false),
            Column::make('status'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Projects_' . date('YmdHis');
    }
}
