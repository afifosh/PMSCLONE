<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContractsDataTable extends DataTable
{
  public $projectId = null;

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('subject', function ($contract) {
        return view('admin.pages.contracts.name', compact('contract'));
      })
      ->addColumn('action', function ($contract) {
        return view('admin.pages.contracts.action', compact('contract'));
      })
      ->editColumn('company.name', function($project){
        return $project->company ? $project->company->name : '-';
      })
      ->editColumn('project.name', function($project){
        return $project->project ? $project->project->name : '-';
      })
      ->editColumn('type.name', function($project){
        return $project->type ? $project->type->name : '-';
      })
      ->editColumn('value', function($project){
        return $project->value ? $project->value : '-';
      })
      ->editColumn('start_date', function($project){
        return $project->start_date ? $project->start_date->format('d M, Y') : '-';
      })
      ->editColumn('end_date', function($project){
        return $project->end_date ? $project->end_date->format('d M, Y') : '-';
      })
      ->rawColumns(['subject']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Contract $model): QueryBuilder
  {
    $q = $model->with(['project', 'type', 'company'])->withCount('phases')->newQuery();

    if ($this->projectId) {
      $q->where('project_id', $this->projectId);
    }

    return $q;
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Contract</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Contract',
        'data-href' => route('admin.contracts.create',['project' => $this->projectId])
      ]
    ];

    return $this->builder()
      ->setTableId('contracts-table')
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
    if($this->projectId)
    return [
      Column::make('subject'),
      Column::make('type.name')->title('Type'),
      Column::make('value')->title('Value('.config('app.currency').')'),
      Column::make('start_date'),
      Column::make('end_date'),
      Column::make('phases_count')->title('Phases')->searchable(false),
      Column::make('status'),
    ];

    return [
      Column::make('subject'),
      Column::make('company.name')->title('Company'),
      Column::make('project.name')->title('Project'),
      Column::make('type.name')->title('Type'),
      Column::make('value')->title('Value('.config('app.currency').')'),
      Column::make('start_date'),
      Column::make('end_date'),
      Column::make('phases_count')->title('Phases')->searchable(false),
      Column::make('status'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Contracts_' . date('YmdHis');
  }
}
