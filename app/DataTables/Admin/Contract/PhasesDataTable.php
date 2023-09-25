<?php

namespace App\DataTables\Admin\Contract;

use App\Models\ContractPhase;
use App\Models\ContractStage;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PhasesDataTable extends DataTable
{
  public $stage;
  public $contract_id;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
    ->editColumn('action', function($phase){
      return view('admin.pages.contracts.phases.actions', ['phase' => $phase, 'stage' => $this->stage, 'contract_id' => $this->contract_id])->render();
    })
    ->editColumn('estimated_cost', function($phase){
      return Money($phase->estimated_cost, $phase->contract->currency, true);
    })
    ;
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ContractPhase $model): QueryBuilder
  {
    // stage is type of ContractStage
    return $model->when($this->stage instanceof ContractStage, function($q){
      $q->where('stage_id', $this->stage->id);
    })->with(['contract' => function($q){
      $q->select('contracts.id', 'currency');
    }])->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    // if ($this->contract->getRawOriginal('status') == 'Active')
      $buttons[] = [
        'text' => '<span>Add Phase</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Add Phase',
          'data-href' => route('admin.projects.contracts.stages.phases.create', ['project' => 'project', $this->contract_id, $this->stage->id ?? 'stage'])
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
    return 'Phases_' . date('YmdHis');
  }
}
