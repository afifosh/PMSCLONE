<?php

namespace App\DataTables\Admin\Finance;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TaxesDataTable extends DataTable
{
  public $type = 'Tax';

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('action', function($tax){
        return view('admin.pages.finances.taxes.action', compact('tax'));
      })
      ->setRowId('id');
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Tax $model): QueryBuilder
  {
    return $model->where('is_retention', $this->type == 'Retention')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add '. $this->type .'</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add '.$this->type,
        'data-href' => route('admin.finances.taxes.create', ['type' => strtolower($this->type)])
      ]
    ];

    return $this->builder()
      ->setTableId('taxes-table')
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
      Column::make('amount'),
      Column::make('type'),
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
    return 'Taxes_' . date('YmdHis');
  }
}
