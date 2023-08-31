<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class NotifiableUsersDataTable extends DataTable
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
    ->addColumn('user', function ($user) {
      return view('admin._partials.sections.user-info', ['user' => $user]);
    })
    ->addColumn('action', function ($admin) {
      return view('admin.pages.contracts.settings.notifiable-users.action', ['admin' => $admin, 'contract' => $this->contract]);
    });
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Admin $model): QueryBuilder
  {
    return $model->whereHas('contractNotifiableUser', function ($q) {
      $q->where('contract_id', $this->contract->id);
    })->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add User</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add User',
        'data-href' => route('admin.contracts.notifiable-users.create', $this->contract->id)
      ]
    ];

    return $this->builder()
      ->setTableId('contract-notifiable-users-table')
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
      Column::make('user')
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'NotifiableUsers_' . date('YmdHis');
  }
}