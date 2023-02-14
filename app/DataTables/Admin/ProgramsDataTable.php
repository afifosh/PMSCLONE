<?php

namespace App\DataTables\Admin;

use App\Models\Program;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProgramsDataTable extends DataTable
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
      ->editColumn('name', function ($row) {
        return '<a href="'.route('admin.programs.show', $row).'">'.htmlspecialchars(substr($row->name, 0, 15), ENT_QUOTES, 'UTF-8').'</a>';
      })
      ->addColumn('parent', function (Program $program) {
        return @$program->parent->name ?? '-';
      })
      ->editColumn('description', function ($program) {
        return substr($program->description, 0, 15);
      })
      ->addColumn('action', function (Program $program) {
        return view('admin.pages.programs.action', compact('program'));
      })
      ->setRowId('id')
      ->rawColumns(['name','action']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\Program $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(Program $programs): QueryBuilder
  {
    return $programs->mine();
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
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Program</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Create New Program',
          'data-href' => route('admin.programs.create')
        ]
      ];
    return $this->builder()
      ->setTableId(Program::DT_ID)
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
      Column::make('name')->title('Program Name'),
      Column::make('parent'),
      Column::make('program_code'),
      Column::make('description'),
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
    return 'Programs_' . date('YmdHis');
  }
}
