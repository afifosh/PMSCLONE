<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class DocumentStatsDataTable extends DataTable
{
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('contracts.id', function ($contract) {
        return view('admin.pages.contracts.name', ['contract_id' => $contract->id]);
      })
      ->addColumn('requested_docs_count', function ($contract) {
        return $contract->requestedDocs()->count();
      })
      ->addColumn('pending_docs_count', function ($contract) {
        return $contract->pendingDocs()->count();
      })
      ->setRowId('id');
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Contract $model): QueryBuilder
  {
    $q = $model->withCount([
      'uploadedDocs as active_docs_count' => function ($q) {
        $q->where('expiry_date', '>', today());
      },
      'uploadedDocs as expired_docs_count' => function ($q) {
        $q->where('expiry_date', '<=', today());
      }
    ])->applyRequestFilters()->newQuery();

    return $q;
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('contract-doc-stats-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom(
        '
        <"row mx-2"<"col-md-2"<"me-3"l>>
        <"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>
        >t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
      )
      // ->addAction(['width' => '80px'])
      ->orderBy([0, 'DESC'])
      ->responsive(true)
      ->parameters([
        'buttons' => [],
        "scrollX" => true
      ]);
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    return [
      Column::make('contracts.id')->title('Contract'),
      Column::make('status'),
      Column::make('requested_docs_count')->title('Requested Docs'),
      Column::make('pending_docs_count')->title('Pending Docs'),
      Column::make('active_docs_count')->title('Active Docs'),
      Column::make('expired_docs_count')->title('Expired Docs'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'DocumentStats_' . date('YmdHis');
  }
}
