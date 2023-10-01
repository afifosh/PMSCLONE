<?php

namespace App\DataTables\Admin\RFP;

use App\Models\RFPDraft;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DraftRfpsDataTable extends DataTable
{
  protected $program_id;

  public function setProgram($program_id)
  {
    $this->program_id = $program_id;
  }
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
        return '<a href="' . route('admin.draft-rfps.show', $row->id) . '">' . htmlspecialchars(substr($row->name, 0, 15), ENT_QUOTES, 'UTF-8') . '</a>';
      })
      ->editColumn('description', function ($row) {
        return substr($row->description, 0, 20);
      })
      ->addColumn('action', function (RFPDraft $rfp) {
        return view('admin.pages.rfp.action', compact('rfp'));
      })
      ->addColumn('program', function ($row) {
        return $row->program->name ?? '-';
      })
      ->rawColumns(['name', 'action']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\RFPDraft $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(RFPDraft $model): QueryBuilder
  {
    $q = $model->mine();
    $q->when($this->program_id, function ($q) {
      $q->where('program_id', $this->program_id);
    });
    return $q;
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
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add New</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Add New',
          'data-href' => route('admin.draft-rfps.create',['program_id' => $this->program_id ?? null])
        ]
      ];

    return $this->builder()
      ->setTableId(RFPDraft::DT_ID)
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
   *
   * @return array
   */
  public function getColumns(): array
  {
    return [
      Column::make('id'),
      Column::make('name')->title('RFP Name'),
      Column::make('program'),
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
    return 'DraftRfps_' . date('YmdHis');
  }
}
