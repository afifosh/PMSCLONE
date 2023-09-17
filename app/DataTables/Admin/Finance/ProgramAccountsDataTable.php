<?php

namespace App\DataTables\Admin\Finance;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Support\LaravelBalance\Models\AccountBalance;
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
    ->editColumn('account_number', function($account){
      return '<a href="'.route('admin.finances.program-accounts.transactions.index', $account->id).'">'.$account->account_number.'</a>';
    })
    ->editColumn('balance', function($account){
      return Money::{$account->currency ?? config('money.defaults.currency')}($account->balance, false)->format();
    })
    ->rawColumns(['account_number']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(AccountBalance $model): QueryBuilder
  {
    return $model->has('programs')->with('related.holders')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Account</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Account',
        'data-href' => route('admin.finances.program-accounts.create')
      ]
    ];

    return $this->builder()
      ->setTableId('program-accounts-table')
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
      Column::make('account_number'),
      Column::make('name')->title('Account Name'),
      Column::make('currency'),
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
