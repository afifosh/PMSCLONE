<?php

namespace App\DataTables\Admin\AccessList;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AdminAccessListsDataTable extends DataTable
{
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
      ->addColumn('user_organization', function ($user) {
        return @$user->designation->department->company->name ? view('admin._partials.sections.company-avatar', ['company' => @$user->designation->department->company]) : '-';
      })
      ->addColumn('programs', function ($user) {
        return $user->accessiblePrograms->pluck('name')->join(', ');
      })
      ->addColumn('action', function ($user) {
        return '-';
        return view('admin.pages.programs.users.action', compact('programUser'));
      })
      ->filterColumn('user', function ($query, $keyword) {
        return $query->whereHas('user', function ($q) use ($keyword) {
          return $q->where('email', 'like', "%" . $keyword . "%");
        });
      })
      ->setRowId('id')
      ->rawColumns(['user', 'action']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Admin $model): QueryBuilder
  {
    return $model->has('accessiblePrograms')->newQuery();
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
        'data-href' => route('admin.admin-access-lists.create')
      ]
    ];

    return $this->builder()
      ->setTableId('admin-access-lists-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->responsive(true)
      ->dom(
        '
      <"row mx-2"<"col-md-2"<"me-3"l>>
      <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
      >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      ->addAction(['width' => '80px'])
      ->orderBy([0, 'DESC'])
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
      Column::make('user'),
      Column::make('user_organization')->title('User Organization'),
      Column::make('programs')->title('Accessible Programs'),
      Column::make('created_at'),
      Column::make('updated_at')
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'AdminAccessLists_' . date('YmdHis');
  }
}
