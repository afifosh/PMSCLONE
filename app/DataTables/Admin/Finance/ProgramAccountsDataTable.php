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
    ->editColumn('account_number', function($account) {
          // Use a regular expression to insert '-' after every 4 digits
          $formattedAccountNumber = preg_replace("/(\d{4})(?=\d)/", "$1-",  $account->account_number);
        return '<a href="'.route('admin.finances.program-accounts.transactions.index', $account->id).'">'.$formattedAccountNumber.'</a>';
    })
    ->addColumn('account_holder', function($account) {
        // Ensure that the relationship is loaded to optimize performance
        if (!$account->relationLoaded('programs')) {
          $account->load('programs');
      }

      // // Fetch the names of the programs and concatenate them.
      // return $account->programs->pluck('name')->implode(', ');
        // Iterate through each program and format the output
        // Iterate through each program and format the output
        $programsOutput = $account->programs->map(function($program) {
          $name = htmlspecialchars($program->name, ENT_QUOTES, 'UTF-8');
          $avatarSrc = $program->avatar; // Get the program's avatar
      
          return "<li data-bs-toggle='tooltip' data-popup='tooltip-custom' data-bs-placement='top' class='avatar pull-up' aria-label='$name' data-bs-original-title='$name'>
              <img class='avatar avatar-sm rounded-circle' src='$avatarSrc' alt='$name'/>
          </li>";
      });
      
      return "<ul class='list-unstyled m-0 d-flex align-items-center avatar-group'>" . $programsOutput->implode('') . "</ul>"; // Wrap the list items in an unordered list
 
      
      })->escapeColumns([])

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
      Column::make('account_holder')->title('Account Holder'),
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
