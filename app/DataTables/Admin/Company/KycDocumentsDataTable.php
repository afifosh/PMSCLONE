<?php

namespace App\DataTables\Admin\Company;

use App\Models\KycDocument;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class KycDocumentsDataTable extends DataTable
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
        ->editColumn('required_from', function($kyc_document){
          return $kyc_document->required_from == 1 ? 'Foreign' : ($kyc_document->required_from == 2 ? 'Local' : 'Both');
        })
        ->editColumn('status', function($kyc_document){
          return $kyc_document->status ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-danger">Inactive</span>';
        })
        ->addColumn('action', function($kyc_document){
          return view('admin.pages.kycdocument.action', compact('kyc_document'));
        })
        ->rawColumns(['status', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\KycDocument $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(KycDocument $model): QueryBuilder
    {
        return $model->where('workflow', 'Company Kyc')->newQuery();
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
          'action' => 'function(e, dt, node, config){
            e.preventDefault();
            window.location.href = "'.route('admin.kyc-documents.create').'";
          }'
        ];

      return $this->builder()
        ->setTableId('kycdocuments-table')
        ->columns($this->getColumns())
        ->minifiedAjax($url = '', $script = '', $data = [])
        ->responsive(true)
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
            Column::make('title'),
            Column::make('required_from'),
            Column::make('status'),
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
        return 'KycDocuments_' . date('YmdHis');
    }
}
