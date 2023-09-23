<?php

namespace App\DataTables\Admin\Finance;

use App\Models\Company;
use App\Models\Contract;
use App\Models\InvoicePayment;
use App\Models\Payment;
use Google\Service\AIPlatformNotebooks\Runtime;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PaymentsDataTable extends DataTable
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
      ->editColumn('invoice_id', function ($invoicePayment) {
        return '<a href="' . route('admin.invoices.edit', $invoicePayment->invoice_id) . '">' . runtimeInvIdFormat($invoicePayment->invoice_id) . '</a>';
      })
      ->addColumn('action', function($invoicePayment){
        return view('admin.pages.finances.payment.action', compact('invoicePayment'));
      })
      ->rawColumns(['invoice_id']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(InvoicePayment $model): QueryBuilder
  {
    $query = $model->newQuery();

    if ($this->filterBy instanceof Contract) {
      $query->whereHas('invoice', function ($q) {
        $q->where('contract_id', $this->filterBy->id);
      });
    } else if ($this->filterBy instanceof Company) {
      $query->whereHas('invoice', function ($q) {
        $q->where('company_id', $this->filterBy->id);
      });
    }

    return $query;
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<i class="ti ti-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add Payment</span>',
      'className' =>  'btn btn-primary mx-3',
      'attr' => [
        'data-toggle' => "ajax-modal",
        'data-title' => 'Add Payment',
        'data-href' => route('admin.finances.payments.create')
      ]
    ];

    return $this->builder()
      ->setTableId('payments-table')
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
      Column::make('invoice_id'),
      Column::make('transaction_id'),
      Column::make('amount'),
      Column::make('created_at'),
      Column::make('updated_at'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'Payments_' . date('YmdHis');
  }
}
