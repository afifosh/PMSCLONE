<?php

namespace App\DataTables\Admin\Contract;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\UploadedKycDoc;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UploadedDocsDataTable extends DataTable
{
  /*
  * @var App\Models\Contract | App\Models\Invoice $model
  */
  public $model;

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('action', function ($doc) {
        return view('admin.pages.contracts.uploaded-docs.action', ['doc' => $doc, 'contract' => $this->model]);
      })
      ->editColumn('status', function ($doc) {
        return '<span class="badge bg-label-' . ($doc->status == 'Active' ? 'success' : 'danger') . '">' . $doc->status . '</span>';
      })
      ->addColumn('uploader', function ($doc) {
        return view('admin._partials.sections.user-info', ['user' => $doc->uploader]);
      })
      ->editColumn('requested_doc.title', function ($doc) {
        if ($this->model instanceof Contract)
          return '<a href="' . route('admin.contracts.uploaded-documents.show', [$this->model, $doc]) . '">' . htmlspecialchars($doc->requestedDoc->title, ENT_QUOTES, 'UTF-8') . '</a>';
        else
          return '<a href="' . route('admin.invoices.uploaded-documents.show', [$this->model, $doc]) . '">' . htmlspecialchars($doc->requestedDoc->title, ENT_QUOTES, 'UTF-8') . '</a>';
      })
      ->orderColumn('status', function ($query, $order) {
        $query->orderBy('expiry_date', $order);
      })
      ->rawColumns(['status', 'requested_doc.title']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(UploadedKycDoc $model): QueryBuilder
  {
    $uniqueId = UploadedKycDoc::where(function ($q) {
      $q->where('doc_requestable_type', $this->model::class)
        ->where('doc_requestable_id', $this->model->id);
    })
      ->when($this->model::class == Invoice::class, function ($q) {
        $q->orWhere(function ($q) {
          $q->whereHas('kycDoc', function ($q) {
            $q->where('is_global', true);
          })
            ->whereHasMorph('docRequestable', [$this->model::class], function ($q) {
              $q->whereHas('contract', function ($q) {
                $q->where('id', $this->model->contract_id);
              });
            });
        });
      })
      ->selectRaw('MAX(id) as id')
      ->groupBy('kyc_doc_id')
      ->pluck('id')->toArray();

    return $model->whereIn('id', $uniqueId)
      ->with([
        'requestedDoc',
        'uploader',
        'versions' => function ($q) {
          $q->select(['id', 'kyc_doc_id'])->orderBy('id', 'DESC');
        }
      ])->withCount('versions')->newQuery();
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
      Column::make('id'),
      Column::make('requested_doc.title')->title('Title')->sortable(false)->searchable(false),
      Column::make('uploader'),
      Column::make('status')->searchable(false),
      Column::make('expiry_date'),
      Column::make('versions_count')->title('Versions')->searchable(false),
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
