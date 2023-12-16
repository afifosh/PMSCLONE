<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceStatusController extends Controller
{
    public function __construct()
    {
      $this->middleware('permission:update invoice|create invoice')->only(['create', 'store']);
    }

    public function create(Invoice $invoice)
    {
      // if invoice has paid status, then it can't be changed to any other status
      if ($invoice->status == 'Paid' || $invoice->status == 'Partial Paid') {
        return $this->sendError('Invoice is status, so it can\'t be changed to any other status');
      }

      return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.status.edit', compact('invoice'))->render()]);
    }

    public function store(Invoice $invoice, Request $request)
    {
      // if invoice has paid status, then it can't be changed to any other status
      if ($invoice->status == 'Paid' || $invoice->status == 'Partial Paid') {
        return $this->sendError('Invoice is status, so it can\'t be changed to any other status');
      }

      $request->validate([
        'void_reason' => 'required',
      ]);

      DB::beginTransaction();
      try{
        $invoice->update([
          'status' => 'Void',
          'void_reason' => $request->void_reason,
        ]);

        if($invoice->authorityInvoice)
        {
          $invoice->authorityInvoice->update([
            'status' => 'Void',
            'void_reason' => $request->void_reason,
          ]);
        }
      } catch (\Exception $e) {
        DB::rollBack();
        return $this->sendError($e->getMessage());
      }
      DB::commit();

      return $this->sendRes('Updated Successfully', ['event' => 'page_reload', 'close' => 'globalModal']);
    }
}
