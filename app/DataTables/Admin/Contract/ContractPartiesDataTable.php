<?php

namespace App\DataTables\Admin\Contract;

use App\Models\ContractParty;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContractPartiesDataTable extends DataTable
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
      ->editColumn('contract_id', function($contractParty){
        return runtimeContractIdFormat($contractParty->contract_id);
      })
      ->editColumn('party', function($contractParty){
        return $contractParty->party->name;
      })
      ->addColumn('action', function ($contractParty) {
        return view('admin.pages.contracts.contract-parties.action', ['contract' => $this->contract, 'contractParty' => $contractParty]);
      });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ContractParty $model): QueryBuilder
  {
    return $model->where('contract_id', $this->contract->id)->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Party</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add Party',
        'data-href' => route('admin.contracts.contract-parties.create', ['contract' => $this->contract])
      ]
    ];

    return $this->builder()
      ->setTableId('contract-parties-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
        <"row mx-2"<"col-md-2"<"me-3"l>>
        <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
        >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->orderBy([0, 'desc'])
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
      Column::make('id'),
      Column::make('contract_id')->title('Contract'),
      Column::make('party'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'ContractParties_' . date('YmdHis');
  }
}
