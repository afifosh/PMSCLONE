<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\PaymentsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\PaymentStoreRequest;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractCategory;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;

class PaymentController extends Controller
{
  public function index(PaymentsDataTable $dataTable, null|string $model = null)
  {
    $data = [];

    if (request()->route()->getName() == 'admin.companies.payments.index') {
      $dataTable->filterBy = Company::findOrFail(request()->route('company'));
      $data['company'] = $dataTable->filterBy;
    } elseif (request()->route()->getName() == 'admin.contracts.payments.index') {
      $dataTable->filterBy = Contract::findOrFail(request()->route('contract'));
      $data['contract'] = $dataTable->filterBy;
    } elseif (request()->route()->getName() == 'admin.invoices.payments.index' && request()->accepts == 'view_data') {
      $data['invoice'] = Invoice::with('payments')->findOrFail(request()->route('invoice'));
      return $this->sendRes('success', ['view_data' => view('admin.pages.finances.payment.index-table', $data)->render()]);
    }else{
      $data['companies'] =Company::has('contracts.invoices.payments')->get(['name', 'id', 'type'])->prepend('All', '');
      $data['invoice_types'] = ['' => 'All', 'Regular' => 'Regular', 'Down payment' => 'Down payment'];
      $data['contract_categories'] = ContractCategory::pluck('name', 'id')->prepend('All', '');
    }

    return $dataTable->render('admin.pages.finances.payment.index', $data);
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
      $payment->invoice_id => runtimeInvIdFormat($payment->invoice_id) . ' - Unpaid ' . $data['invoice']->total - $data['invoice']->paid_amount
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
