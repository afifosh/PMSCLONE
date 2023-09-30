<?php

namespace App\DataTables\Admin\Contract;

use App\Models\UploadedDoc;
use App\Models\UploadedKycDoc;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UploadedDocsDataTable extends DataTable
{
  /*
  * @var Contract $contract
  */
  public $contract;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('action', function ($doc) {
        return view('admin.pages.contracts.uploaded-docs.action', ['doc' => $doc, 'contract' => $this->contract]);
      })
      ->editColumn('status', function ($doc) {
        return '<span class="badge bg-label-' . ($doc->status == 'Active' ? 'success' : 'danger') . '">' . $doc->status . '</span>';
      })
      ->addColumn('uploader', function ($doc) {
        return view('admin._partials.sections.user-info', ['user' => $doc->uploader]);
      })
      ->rawColumns(['status']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(UploadedKycDoc $model): QueryBuilder
  {
    return $model->with('requestedDoc', 'uploader')->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('uploaded-docs-table')
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
      Column::make('id'),
      Column::make('requested_doc.title')->title('Title'),
      Column::make('uploader'),
      Column::make('status'),
      Column::make('expiry_date'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'UploadedDocs_' . date('YmdHis');
  }
}
