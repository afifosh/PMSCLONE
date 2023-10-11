<?php

namespace App\DataTables\Admin\Finance;

use App\Models\FinancialYear;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Akaunting\Money\Money;

class FinancialYearsDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('account_number', function($financialYear){
          // Use a regular expression to insert '-' after every 4 digits
          $accountNumber = $financialYear->defaultCurrencyAccount[0]->account_number;
          $formattedAccountNumber = preg_replace("/(\d{4})(?=\d)/", "$1-", $accountNumber);
        return $formattedAccountNumber;
      })
      ->editColumn('label', function($financialYear){
        return view('admin.pages.finances.financial-years.label', compact('financialYear'));
      })
      ->editColumn('action', function($financialYear){
        return view('admin.pages.finances.financial-years.action', compact('financialYear'));
      })
      ->addColumn('balance', function($financialYear){
        return Money($financialYear->defaultCurrencyAccount[0]->balance, $financialYear->defaultCurrencyAccount[0]->currency ?? config('money.defaults.currency'), true);
        return Money::{$financialYear->defaultCurrencyAccount[0]->currency ?? config('money.defaults.currency')}($financialYear->defaultCurrencyAccount[0]->balance, true)->format();
      });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(FinancialYear $model): QueryBuilder
  {
    return $model->with('defaultCurrencyAccount')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Financial Year</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Financial Year',
        'data-href' => route('admin.finances.financial-years.create')
      ]
    ];

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
      Column::make('account_number'),
      // Column::make('label'),
      Column::make('start_date'),
      Column::make('end_date'),
      Column::make('initial_balance'),
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
    return 'FinancialYears_' . date('YmdHis');
  }
}
