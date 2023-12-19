<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\PaymentsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Finance\PaymentStoreRequest;
use App\Models\AuthorityInvoice;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractCategory;
use App\Models\Invoice;
use App\Models\InvoiceConfig;
use App\Models\Program;
use App\Models\InvoicePayment;
use App\Support\LaravelBalance\Dto\TransactionDto;
use App\Support\LaravelBalance\Models\AccountBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\LaravelBalance\Services\TransactionProcessor;

class PaymentController extends Controller
{
  /**
   * @var TransactionProcessor
   */
  private $transactionProcessor;

  public function __construct(TransactionProcessor $transactionProcessor)
  {
    $this->middleware('permission:read payment', ['only' => ['index', 'show']]);
    $this->middleware('permission:create payment', ['only' => ['create', 'store']]);
    $this->middleware('permission:update payment', ['only' => ['edit', 'update']]);
    $this->middleware('permission:delete payment', ['only' => ['destroy']]);

    $this->transactionProcessor = $transactionProcessor;
  }

  public function index(PaymentsDataTable $dataTable, null|string $model = null)
  {
    $data = [];

    if (request()->route()->getName() == 'admin.companies.payments.index') {
      $dataTable->filterBy = Company::findOrFail(request()->route('company'));
      $data['company'] = $dataTable->filterBy;
    } elseif (request()->route()->getName() == 'admin.contracts.payments.index') {
      $dataTable->filterBy = Contract::validAccessibleByAdmin(auth()->id())->findOrFail(request()->route('contract'));
      $data['contract'] = $dataTable->filterBy;
    } elseif (request()->route()->getName() == 'admin.programs.payments.index') {
      $dataTable->filterBy = Program::findOrFail(request()->route('program'));
      $data['program'] = $dataTable->filterBy;
    } elseif (request()->route()->getName() == 'admin.invoices.payments.index' && request()->accepts == 'view_data') {
      $data['invoice'] = Invoice::validAccessibleByAdmin(auth()->id())->with('payments')->findOrFail(request()->route('invoice'));
      return $this->sendRes('success', ['view_data' => view('admin.pages.finances.payment.index-table', $data)->render()]);
    } else {
      $data['companies'] = Company::has('contracts.invoices.payments')->get(['name', 'id', 'type'])->prepend('All', '');
      $data['contract_categories'] = ContractCategory::pluck('name', 'id')->prepend('All', '');
    }

    $data['invoice_types'] = ['' => 'All', 'Regular' => 'Regular', 'Down payment' => 'Down payment'];

    return $dataTable->render('admin.pages.finances.payment.index', $data);
    // return view('admin.pages.finances.payment.index');
  }

  public function create(Request $request)
  {
    $data['invoicePayment'] = new InvoicePayment();
    $data['retentions'] = InvoiceConfig::where('config_type', 'Retention')->where('status', 1)->get();
    if ($request->invoice && $request->type != 'tax-authority') {
      $data['invoice'] = Invoice::with(['contract.assignable'])->findOrFail($request->invoice);
      $data['companies'] = [$data['invoice']->contract->assignable_id => $data['invoice']->contract->assignable->name];
      $data['contracts'] = [$data['invoice']->contract_id => $data['invoice']->contract->subject];
      $data['invoiceId'] = [
        $request->invoice => runtimeInvIdFormat($request->invoice) . ' - Unpaid ' . $data['invoice']->total - $data['invoice']->paid_amount - $data['invoice']->downpayment_amount
      ];
      $data['selected_invoice'] = $request->invoice;
      $data['event'] = 'page_reload';
    } else if ($request->invoice && $request->type == 'tax-authority') {
      $data['invoice'] = AuthorityInvoice::with('invoice.contract.assignable')->findOrFail($request->invoice);
      $data['companies'] = [$data['invoice']->invoice->contract->assignable_id => $data['invoice']->invoice->contract->assignable->name];
      $data['contracts'] = [$data['invoice']->invoice->contract_id => $data['invoice']->invoice->contract->subject];
      $data['invoiceId'] = [
        $request->invoice => runtimeTAInvIdFormat($request->invoice) . ' - Unpaid ' . $data['invoice']->total - $data['invoice']->paid_amount - $data['invoice']->downpayment_amount
      ];
      $data['invoice_type'] = 'AuthorityInvoice';
      $data['selected_invoice'] = $request->invoice;
      $data['table_id'] = 'authority-invoices-table';
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.payment.create', $data)->render()]);
  }

  public function store(PaymentStoreRequest $req)
  {
    DB::beginTransaction();

    try {
      // create debit transaction for related account balance
      $trx = $this->transactionProcessor->create(
        AccountBalance::find($req->account_balance_id),
        new TransactionDto(
          -$req->amount,
          'Debit',
          'Invoice Payment',
          'Invoice Payment',
          ['data' => ['payment_type' => $req->payment_type]],
          ['type' => $req->invoice::class, 'id' => $req->invoice_id]
        )
      );

      // create payment
      $req->invoice->payments()->create(['ba_trx_id' => $trx->id] + $req->validated());

      if ($req->invoice::class == Invoice::class) {
        $req->invoice->update([
          'paid_amount' => $req->invoice->paid_amount + $req->amount,
          'status' => $req->invoice->paid_amount + $req->amount >= $req->invoice->total ? 'Paid' : 'Partial Paid',
        ]);

        // release retention if requested
        if ($req->release_retention == 'This') {
          $req->invoice->releaseRetention($req->account_balance_id);
        } else if ($req->release_retention == 'All') {
          $req->invoice->load('contract.invoices');
          $req->invoice->contract->releaseInvoicesRetentions($req->account_balance_id);
        }
      } else {
        if ($req->payment_type == 'wht') {
          $data['paid_wht_amount'] = $req->invoice->paid_wht_amount + $req->amount;
        } else if ($req->payment_type == 'rc') {
          $data['paid_rc_amount'] = $req->invoice->paid_rc_amount + $req->amount;
        } else if ($req->payment_type == 'Full') {
          $data['paid_wht_amount'] = $req->invoice->paid_wht_amount;
          $data['paid_rc_amount'] = $req->invoice->paid_rc_amount;
        }
        $req->invoice->update($data + [
          'status' => $req->invoice->paid_wht_amount + $req->invoice->paid_rc_amount + $req->amount >= $req->invoice->total ? 'Paid' : 'Partial Paid'
        ]);
      }

      DB::commit();

      $event = $req->event ? $req->event : 'table_reload';
      $table_id = $req->table_id ? $req->table_id : 'payments-table';

      return $this->sendRes('Payment created successfully.', ['event' => $event, 'table_id' => $table_id, 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function edit(InvoicePayment $payment)
  {
    $data['invoicePayment'] = $payment;
    $data['invoice'] = $payment->payable;
    if ($payment->payable_type == Invoice::class) {
      $data['invoiceId'] = [
        $payment->payable_id => runtimeInvIdFormat($payment->payable_id) . ' - Unpaid ' . $data['invoice']->total - $data['invoice']->paid_amount - $data['invoice']->downpayment_amount
      ];
    } elseif ($payment->payable_type == AuthorityInvoice::class) {
      $data['invoiceId'] = [
        $payment->payable_id => runtimeTAInvIdFormat($payment->payable_id) . ' - Unpaid ' . $data['invoice']->total - $data['invoice']->paid_amount - $data['invoice']->downpayment_amount
      ];
      $data['invoice_type'] = 'AuthorityInvoice';
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.payment.create', $data)->render()]);
  }

  public function update(PaymentStoreRequest $request, InvoicePayment $payment)
  {
    $invoice = $payment->invoice;
    $invoice->update([
      'paid_amount' => $invoice->paid_amount - $payment->amount + $request->amount,
      'status' => $invoice->paid_amount - $payment->amount + $request->amount >= $invoice->total ? 'Paid' : 'Partial Paid',
    ]);

    $payment->update($request->validated());

    return $this->sendRes('Payment updated successfully.', ['event' => 'table_reload', 'table_id' => 'payments-table', 'close' => 'globalModal']);
  }

  public function destroy($payment)
  {
    if ($payment != 'bulk') {
      $payment = InvoicePayment::with('payable')->findOrFail($payment);
      $this->deletePayment($payment);
    } else {
      $payments = InvoicePayment::whereIn('id', request()->payments)->with('payable')->get();
      foreach ($payments as $payment) {
        $this->deletePayment($payment);
      }
    }

    return $this->sendRes('Payment deleted successfully.', ['event' => 'table_reload', 'table_id' => 'payments-table']);
  }

  private function deletePayment(InvoicePayment $payment): void
  {
    $invoice = $payment->payable_type == Invoice::class ? $payment->payable : $payment->payable->invoice;
    if ($payment->type == 1) {
      $invoice->undoRetentionRelease();
      $payment->delete();
    } else {
      $status = '';
      if ($invoice->paid_amount - $payment->amount >= $invoice->payableAmount() + $invoice->paid_amount) {
        $status = 'Paid';
      } else if ($invoice->paid_amount - $payment->amount > 0) {
        $status = 'Partial Paid';
      } else {
        $status = 'Draft';
      }

      $invoice->update([
        'paid_amount' => $invoice->paid_amount - $payment->amount,
        'status' =>  $status,
      ]);

      $payment->delete();
    }
  }
}
