<?php

namespace App\DataTables\Admin;

use App\Models\ProgramUser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProgramUsersDataTable extends DataTable
{
  /**
   * Build DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   * @return \Yajra\DataTables\EloquentDataTable
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('user', function ($row) {
        return "<img class='avatar avatar-sm pull-up rounded-circle' src='".$row->user->avatar."' alt='Avatar'><span class='mx-2'>" . htmlspecialchars($row->user->full_name, ENT_QUOTES, 'UTF-8') . "</span>";
      })
      ->editColumn('added_by', function($row){
        return $row->addedBy->full_name ?? '-';
      })
      ->addColumn('user_organization', function($row){
        return $row->user->company->name ?? '-';
      })
      ->addColumn('action', function (ProgramUser $programUser) {
        return view('admin.pages.programs.users.action', compact('programUser'));
      })
      ->setRowId('id')
      ->rawColumns(['user', 'action']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\ProgramUser $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(ProgramUser $model): QueryBuilder
  {
    $query = $model->query();
    $query->when(request()->program, function($query){
      return $query->where('program_id', request()->program->id)->orWhere('program_id', request()->program->parent_id);
    });
    return $query;
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    if (auth('admin')->user()->can(true))
      $buttons[] = [
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add User</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-offcanvas",
          'data-title' => 'Add User',
          'data-href' => route('admin.programs.users.create', ['program' => request()->program])
        ]
      ];

    return $this->builder()
      ->setTableId(ProgramUser::DT_ID)
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
      ->parameters([
        'buttons' => $buttons,
        "scrollX" => true
      ]);
  }

  /**
   * Get the dataTable columns definition.
   *
   * @return array
   */
  public function getColumns(): array
  {
    return [
      Column::make('id'),
      Column::make('user'),
      Column::make('added_by'),
      Column::make('user_organization')->title('User Organization'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get filename for export.
   *
   * @return string
   */
  protected function filename(): string
  {
    return 'ProgramUsers_' . date('YmdHis');
  }
}
