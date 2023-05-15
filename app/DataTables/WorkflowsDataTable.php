<?php

namespace App\DataTables;

use App\Models\Workflow;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class WorkflowsDataTable extends DataTable
{
  public $slug;

  /**
   * Build DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   * @return \Yajra\DataTables\EloquentDataTable
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
    ->addColumn('action', function($workflow){
      return view('admin.pages.workflow.approval-workflow.action', compact('workflow'));
    })
    ->rawColumns(['name']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\Workflow $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(Workflow $model): QueryBuilder
  {
    $query = $model->newQuery();

    $query->when($this->slug, function ($query) {
      $query->where('slug', $this->slug);
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
    return $this->builder()
      ->setTableId('workflows-table')
      ->columns($this->getColumns())
      ->addAction(['width' => '80px'])
      ->minifiedAjax()
      ->responsive(true)
      ->orderBy(1);
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
    return 'Workflows_' . date('YmdHis');
  }
}
