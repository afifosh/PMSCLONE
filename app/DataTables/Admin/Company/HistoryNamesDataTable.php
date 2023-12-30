<?php

namespace App\DataTables\Admin\Company;

use App\Models\Company;
use App\Models\ModelHistoryName;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class HistoryNamesDataTable extends DataTable
{
  /**
   * @var App\Models\Company
   */
  public $company = null;

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('action', function ($historyName) {
        return view('admin.pages.company.history_names.actions', compact('historyName'));
      })
      ->addIndexColumn();
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(ModelHistoryName $model): QueryBuilder
  {
    return $model->where('model_id', $this->company->id)->where('model_type', Company::class);
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Update Name</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Update Name',
        'data-href' => route('admin.companies.names.create', ['company' => $this->company->id]),
      ]
    ];
    return $this->builder()
      ->setTableId('historynames-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->responsive(true)
      ->addAction()
      // sort by 0 descending
      ->orderBy([2, 'desc'])
      ->dom(
        '
      <"row mx-2"<"col-md-2"<"me-3"l>>
      <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
      >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
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
      Column::computed('DT_RowIndex', 'No.')->addClass('text-center'),
      Column::make('name'),
      Column::make('name_ar'),
      Column::make('created_at')->title('Changed At'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'HistoryNames_' . date('YmdHis');
  }
}
