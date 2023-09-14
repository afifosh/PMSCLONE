<?php

namespace App\DataTables\Admin\Finance;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Vuer\LaravelBalance\Models\AccountBalance;
use Akaunting\Money\Money;

class ProgramAccountsDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
    ->editColumn('created_at', function($account){
      return date(' h:i A d M, Y', strtotime($account->created_at));
    })
    ->editColumn('updated_at', function($account){
      return date(' h:i A d M, Y', strtotime($account->updated_at));
    })
    ->editColumn('balance', function($account){
      return Money::{$account->currency ?? config('money.defaults.currency')}($account->balance, false)->format();
    });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(AccountBalance $model): QueryBuilder
  {
    return $model->where('holder_type', 'App\Models\Program')->with('holder')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];

    return $this->builder()
      ->setTableId('financial-years-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
              <"row mx-2"<"col-md-2"<"me-3"l>>
              <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
              >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      // ->addAction(['width' => '80px'])
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
      Column::make('id'),
      Column::make('holder.name')->title('Program'),
      Column::make('balance'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'ProgramAccounts_' . date('YmdHis');
  }
}
