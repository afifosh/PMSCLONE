<?php

namespace App\DataTables\Admin\Contract;

use App\Models\DocSignature;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DocSignaturesDataTable extends DataTable
{
  /**
   * App\Models\UploadedKycDoc $uploadedDoc
   */
  public $uploadedDoc;
  public $is_signature = true;

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->addColumn('signer', function ($sign){
        if($sign->signer_type == 'App\Models\Admin')
          return view('admin._partials.sections.user-info', ['user' => $sign->signer]);
        else
          '...';
      })
      ->addColumn('action', function($sign){
        return view('admin.pages.docs-signatures.action', compact('sign'));
      })
      ->setRowId('id');
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(DocSignature $model): QueryBuilder
  {
    return $model
      ->where('uploaded_kyc_doc_id', $this->uploadedDoc->id)
      ->where('is_signature', $this->is_signature)
      ->with('signer')
      ->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add '.($this->is_signature ? 'Signature' : 'Stamp').'</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add ' . ($this->is_signature ? 'Signature' : 'Stamp'),
        'data-href' => route('admin.doc-signatures.create', ['doc' => $this->uploadedDoc, 'signature' => $this->is_signature])
      ]
    ];
    return $this->builder()
      ->setTableId($this->getTable(). '-table')
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
        'buttons' => $buttons,
        "scrollX" => true
      ])
      ->ajax(['data' => 'function(d) { d.table = "' . ($this->getTable()) . '"; }']);
  }

  private function getTable(): string
  {
    return $this->is_signature ? 'signatures' : 'stamps';
  }

  /**
   * Get the dataTable columns definition.
   */
  public function getColumns(): array
  {
    if ($this->is_signature)
      return [
        Column::make('id'),
        Column::make('signer'),
        Column::make('signer_position'),
        Column::make('signed_at'),
        Column::make('created_at'),
        Column::make('updated_at'),
      ];
    else
      return [
        Column::make('id'),
        Column::make('signer')->title('Stamped By'),
        Column::make('signer_position')->title('Stamper Position'),
        Column::make('signed_at')->title('Stamped At'),
        Column::make('created_at'),
        Column::make('updated_at'),
      ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'DocSignatures_' . date('YmdHis');
  }
}
