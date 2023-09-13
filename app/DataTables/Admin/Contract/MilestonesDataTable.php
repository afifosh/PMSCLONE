<?php

namespace App\DataTables\Admin\Contract;

use App\Models\ContractMilestone;
use App\Models\Milestone;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MilestonesDataTable extends DataTable
{
  public $phase;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
    ->editColumn('action', function($milestone){
      return view('admin.pages.contracts.milestones.actions', ['milestone' => $milestone, 'phase' => $this->phase])->render();
    });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ContractMilestone $model): QueryBuilder
  {
    return $model->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    // if ($this->contract->getRawOriginal('status') == 'Active')
      $buttons[] = [
        'text' => '<span>Add Milestone</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Add Milestone',
          'data-href' => route('admin.projects.contracts.phases.milestones.create', ['project' => 'project', $this->phase->contract_id, $this->phase->id])
        ]
      ];

    return $this->builder()
      ->setTableId('milstones-table')
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
    return [
      Column::make('name'),
      Column::make('start_date'),
      Column::make('due_date')->title('End Date'),
      Column::make('estimated_cost'),
      Column::make('status'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Milestones_' . date('YmdHis');
  }
}
