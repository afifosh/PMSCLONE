<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceStatusController extends Controller
{
    public function create(Invoice $invoice)
    {
      return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.status.edit', compact('invoice'))->render()]);
    }

    public function store(Invoice $invoice, Request $request)
    {
      $request->validate([
        'void_reason' => 'required',
      ]);

      $invoice->update([
        'status' => 'Void',
        'void_reason' => $request->void_reason,
      ]);

      return $this->sendRes('Updated Successfully', ['event' => 'page_reload', 'close' => 'globalModal']);
    }
}
