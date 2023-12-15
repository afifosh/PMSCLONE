<?php

namespace App\DataTables\Admin\Invoice;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PartialInvoicesDataTable extends DataTable
{
  public $partialInvoice = null;
  /**
   * Build the DataTable class.
   *
   * @param QueryBuilder $query Results from query() method.
   */
  public function dataTable(QueryBuilder $query): EloquentDataTable
  {
    return (new EloquentDataTable($query))
      ->editColumn('id', function ($invoice) {
        return '<a href="' . route('admin.invoices.edit', $invoice->id) . '">' . runtimeInvIdFormat($invoice->id) . '</a>';
      })
      ->editColumn('invoice.company_id', function ($invoice) {
        return view('admin._partials.sections.company-avatar', ['company' => $invoice->company])->render();
      })
      ->editColumn('invoice.contract_id', function ($invoice) {
        return $invoice->contract->subject;
      })
      ->editColumn('invoice_total', function (Invoice $invoice) {
        return cMoney($invoice->total, $this->partialInvoice->contract->currency, true);
      })
      ->editColumn('paid_amount', function (Invoice $invoice) {
        return cMoney($invoice->paid_amount, $this->partialInvoice->contract->currency, true);
      })
      ->rawColumns(['invoice.company_id', 'id']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(Invoice $model): QueryBuilder
  {
    return $model->where('type', 'Partial Invoice')->where('id', '!=', $this->partialInvoice->id)->whereHas('phaseItems', function ($q) {
      $q->where('invoiceable_id', $this->partialInvoice->phaseItems[0]->invoiceable_id);
    })->newQuery();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    return $this->builder()
      ->setTableId('downpayments-table')
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
      Column::make('id'),
      Column::make('invoice.company_id')->title('Client'),
      Column::make('invoice.contract_id')->title('Contract'),
      Column::make('due_date')->title('Due Date'),
      Column::make('status')->title('Status'),
      Column::make('invoice_total')->title('Invoice Total'),
      Column::make('paid_amount')->title('Paid Amount'),
      Column::make('created_at')->title('Created At'),
      Column::make('updated_at')->title('Updated At'),
    ];
  }

  /**
   * Get the filename for export.
   */
  protected function filename(): string
  {
    return 'PartialInvoices_' . date('YmdHis');
  }
}
