<?php

namespace App\DataTables\Admin\Finance;

use App\Models\AuthorityInvoice;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Program;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PaymentsDataTable extends DataTable
{
  /*
  * @var null|App\Models\Company|App\Models\Contract|App\Models\Invoice
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
        if($invoicePayment->payable_type == Invoice::class)
        return '<a href="' . route('admin.invoices.edit', $invoicePayment->payable_id) . '">' . runtimeInvIdFormat($invoicePayment->payable_id) . '</a>';
        elseif($invoicePayment->payable_type == AuthorityInvoice::class)
        return '<a href="' . route('admin.invoices.edit', [$invoicePayment->payable->invoice_id, 'tab' => 'authority-tax']) . '">' . runtimeTAInvIdFormat($invoicePayment->payable_id) . '</a>';
      })
      ->editColumn('checkbox', function ($payment) {
        return '<input class="form-check-input payment-check" name="selected_payments[]" type="checkbox" value="' . $payment->id . '">';
      })
      ->editColumn('contract.id', function ($invoicePayment) {
        if($invoicePayment->payable_type == Invoice::class)
        return '<a href="' . route('admin.contracts.show', $invoicePayment->payable->contract->id) . '">' . runtimeContractIdFormat($invoicePayment->payable->contract->id) . '</a>';
        elseif($invoicePayment->payable_type == AuthorityInvoice::class)
        return '<a href="' . route('admin.contracts.show', $invoicePayment->payable->invoice->contract->id) . '">' . runtimeContractIdFormat($invoicePayment->payable->invoice->contract->id) . '</a>';
      })
      ->editColumn('amount', function ($invoicePayment) {
        return cMoney($invoicePayment->amount, $invoicePayment->payable->contract->currency ?? $invoicePayment->payable->invoice->contract->currency, true);
      })
      ->addColumn('action', function($invoicePayment){
        return view('admin.pages.finances.payment.action', compact('invoicePayment'));
      })
      ->rawColumns(['invoice_id', 'contract.id', 'checkbox']);
  }

  /**
   * Get the query source of dataTable.
   */
  public function query(InvoicePayment $model): QueryBuilder
  {
    $query = $model->newQuery();

    if ($this->filterBy instanceof Contract) {
      $query->whereHasMorph('payable', Invoice::class, function ($q) {
        $q->where('contract_id', $this->filterBy->id);
      })->orWhereHasMorph('payable', AuthorityInvoice::class, function ($q) {
        $q->whereHas('invoice', function ($q) {
          $q->where('contract_id', $this->filterBy->id);
        });
      });
    } else if ($this->filterBy instanceof Company) {
      $query->whereHasMorph('payable', Invoice::class, function ($q) {
        $q->whereHas('contract', function ($q) {
          $q->where('company_id', $this->filterBy->id);
        });
      })->orWhereHasMorph('payable', AuthorityInvoice::class, function ($q) {
        $q->whereHas('invoice', function ($q) {
          $q->whereHas('contract', function ($q) {
            $q->where('company_id', $this->filterBy->id);
          });
        });
      });
    } else if ($this->filterBy instanceof Program) {
        // Fetch child program IDs
        $childProgramIds = Program::where('parent_id', $this->filterBy->id)->pluck('id')->toArray();

        // Include the main program's ID
        $programIds = array_merge([$this->filterBy->id], $childProgramIds);

        $query->whereHasMorph('payable', Invoice::class, function ($q) use ($programIds) {
            $q->whereIn('program_id', $programIds);
        });
    } else if($this->filterBy instanceof Invoice){
      $query->whereHasMorph('payable', Invoice::class, function ($q) {
        $q->where('id', $this->filterBy->id);
      })->orWhereHasMorph('payable', AuthorityInvoice::class, function ($q) {
        $q->whereHas('invoice', function ($q) {
          $q->where('id', $this->filterBy->id);
        });
      });
    }

    return $query->applyRequestFilters();
  }

  /**
   * Optional method if you want to use the html builder.
   */
  public function html(): HtmlBuilder
  {
    $buttons = [];
    $buttons[] = [
      'text' => '<span>Select Payments</span>',
      'className' =>  'btn btn-primary mx-3 select-payments-btn',
      'attr' => [
        'onclick' => 'toggleCheckboxes()',
      ]
    ];
    $buttons[] = [
      'text' => '<span>Delete Payments</span>',
      'className' =>  'btn btn-primary mx-3 delete-inv-btn d-none',
      'attr' => [
        'data-toggle' => "confirm-action",
        'data-success-action' => 'destroyBulkPayments',
      ]
    ];
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
      ->orderBy([0, 'DESC'])
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
      Column::make('checkbox')->title('<input class="form-check-input payment-check-all" type="checkbox">')->orderable(false)->searchable(false)->printable(false)->exportable(false)->visible(false)->width(1),
      Column::make('id'),
      Column::make('contract.id')->title('Contract'),
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
