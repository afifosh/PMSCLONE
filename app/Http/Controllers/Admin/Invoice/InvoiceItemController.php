<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\ContractMilestone;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
  public function index(Invoice $invoice)
  {
    $data['invoice'] = $invoice;

    if(request()->mode == 'edit'){
      $data['invoice']->load('items.invoiceable');

      return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit-list', $data)->render()]);
    }

    return view('admin.pages.invoices.items.index', $data);
  }

  public function create(Invoice $invoice)
  {
    $data['invoice'] = $invoice;

    $data['milestones'] = $invoice->contract->milestones;

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.create', $data)->render()]);
  }

  public function store(Request $request, Invoice $invoice)
  {
    $request->validate([
      'milestones' => 'required|array',
      'milestones.*' => 'nullable|exists:contract_milestones,id',
    ]);

    $milestones = filterInputIds($request->milestones);

    $pivot_amounts = ContractMilestone::whereIn('id', $milestones)->pluck('estimated_cost', 'id')->toArray();

    // formate data for pivot table
    $data = [];
    foreach ($milestones as $milestone) {
      $data[$milestone] = ['amount' => $pivot_amounts[$milestone]];
    }

    $invoice->milestones()->syncWithoutDetaching($data);

    return $this->sendRes('success', ['event' => 'functionCall', 'function' => 'reloadMilestonesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, $invoiceItem)
  {
    $invoice->items()->where('id', $invoiceItem)->delete();

    return $this->sendRes('success', ['event' => 'functionCall', 'function' => 'reloadMilestonesList']);
  }
}
