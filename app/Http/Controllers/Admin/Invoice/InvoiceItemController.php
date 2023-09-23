<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\ContractPhase;
use App\Models\Invoice;
use App\Models\Tax;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
  public function index(Invoice $invoice)
  {
    $data['invoice'] = $invoice;
    $data['tax_rates'] = Tax::get();
    if(request()->mode == 'edit'){
      $data['invoice']->load('items.invoiceable');

      return $this->sendRes('success', [
        'view_data' => view('admin.pages.invoices.items.edit-list', $data)->render(),
        'summary' => view('admin.pages.invoices.items.summary', $data)->render(),
        'balance_summary' => view('admin.pages.invoices.balance-summary', $data)->render(),
      ]);
    }

    return view('admin.pages.invoices.items.index', $data);
  }

  public function create(Invoice $invoice)
  {
    $data['invoice'] = $invoice;

    $data['phases'] = $invoice->contract->phases;

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.create', $data)->render()]);
  }

  public function store(Request $request, Invoice $invoice)
  {
    $request->validate([
      'phases' => 'required|array',
      'phases.*' => 'nullable|exists:contract_phases,id',
    ]);

    $phases = filterInputIds($request->phases);

    $pivot_amounts = ContractPhase::whereIn('id', $phases)->pluck('estimated_cost', 'id')->toArray();

    // formate data for pivot table
    $data = [];
    foreach ($phases as $phase) {
      $data[$phase] = ['amount' => $pivot_amounts[$phase]];
    }

    $invoice->phases()->syncWithoutDetaching($data);
    $invoice->updateSubtotal();

    return $this->sendRes('success', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, $invoiceItem)
  {
    $invoice->items()->where('id', $invoiceItem)->delete();
    $invoice->updateSubtotal();

    return $this->sendRes('success', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
  }
}
