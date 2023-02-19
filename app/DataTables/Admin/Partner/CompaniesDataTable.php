<?php

namespace App\DataTables\Admin\Partner;

use App\Models\PartnerCompany;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CompaniesDataTable extends DataTable
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
    ->editColumn('name', function ($row){
      return "<img class='avatar avatar-sm pull-up rounded-circle' src='$row->avatar' alt='Avatar'><span class='mx-2'>".htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8')."</span>";
    })
    ->addColumn('action', function (PartnerCompany $company) {
      return view('admin.pages.partner.companies.action', compact('company'));
    })
    ->setRowId('id')
    ->rawColumns(['name', 'action']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\PartnerCompany $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(PartnerCompany $model): QueryBuilder
  {
    return $model->newQuery();
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
        'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Organization</span>',
        'className' =>  'btn btn-primary mx-3',
        'attr' => [
          'data-toggle' => "ajax-modal",
          'data-title' => 'Add Organization',
          'data-href' => route('admin.partner.companies.create')
        ]
      ];

    return $this->builder()
      ->setTableId(PartnerCompany::DT_ID)
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
      Column::make('name')->title('Organization Name'),
      Column::make('website'),
      Column::make('phone'),
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
    return 'Companies_' . date('YmdHis');
  }
}
