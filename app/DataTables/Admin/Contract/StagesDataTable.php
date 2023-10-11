<?php

namespace App\DataTables\Admin\Contract;

use App\Models\ContractStage;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StagesDataTable extends DataTable
{
  public $contract;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('name', function ($stage) {
        return '<a href="' . route('admin.contracts.stages.phases.index', [$this->contract, $stage]) . '">' . $stage->name . '</a>';
      })
      ->addColumn('total_amount', function ($stage) {
        return view('admin.pages.contracts.stages.value-column', compact('stage'));
      })
      // ->editColumn('total_amount', function ($stage) {
      //   return cMoney($stage->stage_amount, $stage->contract->currency, true);
      // })
      ->addColumn('action', function($stage){
        return view('admin.pages.contracts.stages.actions', compact('stage'));
      })
      ->filterColumn('total_amount', function ($query, $keyword) {
        $query->whereRaw("stage_amount like ?", ["%{$keyword}%"]);
      })
      ->filterColumn('phases_count', function ($query, $keyword){
        $query->has('phases', $keyword);
      })
      ->rawColumns(['name']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ContractStage $model): QueryBuilder
  {
    return $model->where('contract_id', $this->contract->id)->withCount('phases')
    ->with(['contract' => function ($q){
      $q->select(['contracts.id', 'currency']);
    }])->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<span>Add Stage</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add Stage',
        'data-href' => route('admin.contracts.stages.create', ['project' => 'project', $this->contract->id])
      ]
    ];

    return $this->builder()
      ->setTableId('stages-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
        <"row mx-2"<"col-md-2"<"me-3"l>>
        <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
        >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->orderBy([0, 'DESC'])
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
      Column::make('phases_count')->title('Phases'),
      Column::make('status'),
      Column::make('total_amount')->title('Amount')->sortable(false)->searchable(false),
      Column::make('start_date'),
      Column::make('due_date'),
      // Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Stages_' . date('YmdHis');
  }
}
