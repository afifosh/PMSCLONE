<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\InvoiceItemsStoreReqeust;
use App\Http\Requests\Admin\Invoice\InvoiceItemUpdateRequest;
use App\Models\ContractPhase;
use App\Models\ContractStage;
use App\Models\CustomInvoiceItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceConfig;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceItemController extends Controller
{
  public function index(Invoice $invoice)
  {
    $data['invoice'] = $invoice;
    $data['tax_rates'] = InvoiceConfig::get();
    $data['is_editable'] = $invoice->isEditable();

    if (request()->mode == 'edit') {
      $data['invoice']->load('items.invoiceable');
      $data['tab'] = request()->tab;
      return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.tabs.summary', $data)->render()]);

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
    if (request()->item == 'custom') {
      return $this->customItemCreate($invoice);
    }
    // else bulk phase items
    $data['invoice'] = $invoice;

    if (request()->type == 'jsonData') {
      return DataTables::eloquent($invoice->contract->phases()->has('addedAsInvoiceItem', 0)->with('stage'))->toJson();
    }

    return $this->sendRes('success', [
      'view_data' => view('admin.pages.invoices.items.create', $data)->render(), 'JsMethods' => ['initPhasesDataTable']
    ]);
  }

  public function customItemCreate(Invoice $invoice)
  {
    $data['invoice'] = $invoice;
    $data['invoiceItem'] = new CustomInvoiceItem();

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit', $data)->render()]);
  }

  public function storeBulk(InvoiceItemsStoreReqeust $request, Invoice $invoice)
  {
    if (!$invoice->isEditable()) {
      return $this->sendError('Invoice is not editable');
    }

    $phases = filterInputIds($request->phases);

    $invoice->attachPhasesWithTax($phases);

    return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function store(Invoice $invoice, InvoiceItemUpdateRequest $request)
  {
    DB::beginTransaction();
    try {
      $customItem = CustomInvoiceItem::create($request->validated() + ['invoice_id' => $invoice->id]);

      $invoice->items()->create([
        'invoiceable_id' => $customItem->id,
        'invoiceable_type' => CustomInvoiceItem::class,
        'subtotal' => $customItem->subtotal,
        'description' => $request->description,
        'total_tax_amount' => $request->total_tax_amount,
        'manual_tax_amount' => $request->manual_tax_amount,
        'total' => $request->total,
        'rounding_amount' => $request->rounding_amount,
      ]);

      // $item->syncTaxes($request->taxes);

      $invoice->reCalculateTotal();

      DB::commit();

      return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function edit(Invoice $invoice, InvoiceItem $invoiceItem)
  {
    $invoiceItem->load('taxes');
    $data['invoice'] = $invoice;
    $data['invoiceItem'] = $invoiceItem;
    if ($invoiceItem->invoiceable_type == ContractPhase::class) {
      $invoiceItem->load('invoiceable.stage', 'invoiceable.contract');
      $data['phases'] = [$invoiceItem->invoiceable_id => $invoiceItem->invoiceable->name];
      $data['stages'] = ContractStage::where('contract_id', $invoiceItem->invoiceable->contract_id)->pluck('name', 'id');
    } else {
      request()->merge(['item' => 'custom']);
    }

    $data['is_editable'] = $invoice->isEditable();

    if (request()->type == 'edit-form')
      return $this->sendRes('success', [
        'view_data' => view('admin.pages.invoices.items.edit.modal-wrapper', $data)->render(),
      ]);

    if (request()->type == 'reload-modal') {
      $data['tab'] = request()->tab ?? 'summary';
      return $this->sendRes('success', [
        'view_data' => view('admin.pages.invoices.items.edit.table-wrapper', $data)->render(),
      ]);
    }

    return $this->sendRes('success', [
      'view_data' => view('admin.pages.invoices.items.edit.edit-item', $data)->render(),
    ]);
  }

  public function update(Invoice $invoice, InvoiceItem $invoiceItem, InvoiceItemUpdateRequest $request)
  {
    if (!$invoice->isEditable()) {
      return $this->sendError('Invoice is not editable');
    }

    DB::beginTransaction();
    try {
      $invoiceItem->update($request->validated());

      if ($invoiceItem->invoiceable_type == CustomInvoiceItem::class) {
        $invoiceItem->invoiceable->update($request->validated() + ['invoice_id' => $invoice->id]);
      } elseif ($invoiceItem->invoiceable_type == ContractPhase::class) {
        $stage = ContractStage::where('id', $request->stage_id)->firstOr(function () use ($invoiceItem, $request) {
          return ContractStage::create(['contract_id' => $invoiceItem->invoiceable->contract_id, 'name' => $request->stage_id]);
        });

        $invoiceItem->invoiceable->update(['stage_id' => $stage->id, 'estimated_cost' => $request->subtotal] + $request->validated());
      }


      if ($invoiceItem->deduction && $invoiceItem->deduction->is_before_tax) {
        $invoiceItem->recalculateDeductionAmount();
        $invoiceItem->reCalculateTaxAmountsAndResetManualAmounts();
        $invoiceItem->reCalculateTotal();
      } else {
        $invoiceItem->reCalculateTaxAmountsAndResetManualAmounts(false);
        $invoiceItem->reCalculateTotal();
        if ($invoiceItem->deduction) {
          $invoiceItem->recalculateDeductionAmount();
          $invoiceItem->reCalculateTotal();
        }
      }

      $invoice->reCalculateTotal();

      $invoiceItem->syncUpdateWithPhase();

      DB::commit();

      return $this->sendRes('Item Updated Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }



  public function destroy(Invoice $invoice, $invoiceItem, Request $request)
  {
    if (!$invoice->isEditable()) {
      return $this->sendError('Invoice is not editable');
    }

    $request->validate([
      'ids' => 'required|array',
      'ids.*' => 'required|exists:invoice_items,id'
    ]);

    DB::beginTransaction();
    try {
      $items = $invoice->items()->whereIn('invoice_items.id', $request->ids)->get();

      $items->each(function ($item) {
        if ($item->invoiceable_type == CustomInvoiceItem::class)
          $item->invoiceable->delete();

        $item->taxes()->detach();
        $item->deduction()->delete();
        $item->delete();
      });

      $invoice->reCalculateTotal();

      DB::commit();

      return $this->sendRes('Item Removed', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }
}
