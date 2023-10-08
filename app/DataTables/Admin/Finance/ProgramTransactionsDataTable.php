<?php

namespace App\DataTables\Admin\Finance;

use Akaunting\Money\Money;
use App\Support\LaravelBalance\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProgramTransactionsDataTable extends DataTable
{
  public $programAccount;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
    ->editColumn('id', function($transaction){
      return runtimeTransIdFormat($transaction->id);
    })
    ->editColumn('action', function($transaction){
      return view('admin.pages.finances.financial-years.transactions.action', compact('transaction'));
    })
    ->editColumn('type', function($transaction){
      if($transaction->type == 'Credit'){
        return '<span class="badge bg-label-success">Credit</span>';
      }else
        return '<span class="badge bg-label-danger">Debit</span>';
    })
    ->rawColumns(['type']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Transaction $model): QueryBuilder
  {
    return $model->where('account_balance_id', $this->programAccount->id)->with('accountBalance')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];

    return $this->builder()
      ->setTableId('program-transactions-table')
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
      Column::make('id'),
      Column::make('amount'),
      Column::make('type'),
      Column::make('remaining_balance')->title('New Balance'),
      Column::make('title'),
      Column::make('created_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'ProgramTransactions_' . date('YmdHis');
  }
}
