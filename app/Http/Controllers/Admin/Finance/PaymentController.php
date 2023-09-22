<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\PaymentsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\PaymentStoreRequest;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;

class PaymentController extends Controller
{
  public function index(PaymentsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.finances.payment.index');
    // return view('admin.pages.finances.payment.index');
  }

  public function create(Request $request)
  {
    $data['invoicePayment'] = new InvoicePayment();

    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.payment.create', $data)->render()]);
  }

  public function store(PaymentStoreRequest $request)
  {
    $invoice = Invoice::findOrFail($request->invoice_id);

    $invoice->payments()->create($request->validated());

    $invoice->update([
      'paid_amount' => $invoice->paid_amount + $request->amount,
      'status' => $invoice->paid_amount + $request->amount >= $invoice->total ? 'Paid' : 'Partial paid',
    ]);

    return $this->sendRes('Payment created successfully.', ['event' => 'table_reload', 'table_id' => 'payments-table', 'close' => 'globalModal']);
  }

  public function edit(InvoicePayment $payment)
  {
    $data['invoicePayment'] = $payment;
    $data['invoice'] = $payment->invoice;
    $data['invoice'] = [
      $payment->invoice_id => runtimeInvIdFormat($payment->invoice_id) . ' - Unpaid ' . $data['invoice']->total- $data['invoice']->paid_amount
    ];

    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.payment.create', $data)->render()]);
  }

  public function update(PaymentStoreRequest $request, InvoicePayment $payment)
  {
    $invoice = $payment->invoice;
    $invoice->update([
      'paid_amount' => $invoice->paid_amount - $payment->amount + $request->amount,
      'status' => $invoice->paid_amount - $payment->amount + $request->amount >= $invoice->total ? 'Paid' : 'Partial paid',
    ]);

    $payment->update($request->validated());

    return $this->sendRes('Payment updated successfully.', ['event' => 'table_reload', 'table_id' => 'payments-table', 'close' => 'globalModal']);
  }

  public function destroy(InvoicePayment $payment)
  {
    $invoice = $payment->invoice;

    $invoice->update([
      'paid_amount' => $invoice->paid_amount - $payment->amount,
      'status' => $invoice->paid_amount - $payment->amount >= $invoice->total ? 'Paid' : 'Partial paid',
    ]);

    $payment->delete();

    return $this->sendRes('Payment deleted successfully.', ['event' => 'table_reload', 'table_id' => 'payments-table']);
  }
}
