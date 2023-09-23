<?php

namespace App\DataTables\Admin\Invoice;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class InvoicesDataTable extends DataTable
{
  /*
  * @var null|App\Models\Company|App\Models\Contract
  */
  public $filterBy = null;

  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('id', function ($inv) {
        return '<a href="' . route('admin.invoices.edit', $inv->id) . '">' . runtimeInvIdFormat($inv->id) . '</a>';
      })
      ->editColumn('company_id', function ($invoice) {
        return view('admin._partials.sections.company-avatar', ['company' => $invoice->company])->render();
      })
      ->editColumn('contract_id', function ($invoice) {
        return $invoice->contract->subject;
      })
      ->editColumn('action', function ($invoice) {
        return view('admin.pages.invoices.action', ['invoice' => $invoice]);
      })
      ->addColumn('total', function ($invoice) {
        return view('admin.pages.invoices.total-column', ['invoice' => $invoice]);
      })
      ->rawColumns(['company_id', 'id']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Invoice $model): QueryBuilder
  {
    $query = $model->with(['company', 'contract']);
    // if filterBy is instance of Contract
    if ($this->filterBy instanceof Contract) {
      $query->where('contract_id', $this->filterBy->id);
    }else if($this->filterBy instanceof Company){
      $query->where('company_id', $this->filterBy->id);
    }

    return $query->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Create Invoice</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Create Invoice',
        'data-href' => route('admin.invoices.create')
      ]
    ];

    return $this->builder()
      ->setTableId('invoices-table')
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
      Column::make('id'),
      Column::make('company_id')->title('Client'),
      Column::make('contract_id')->title('Contract'),
      Column::make('due_date'),
      Column::make('status'),
      Column::make('total'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Invoices_' . date('YmdHis');
  }
}
