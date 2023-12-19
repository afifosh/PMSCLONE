<div class="d-flex justify-content-between mb-2">
  <span class="me-2">Items Total:</span>
  <span class="fw-semibold">@cMoney($invoice->itemsSubtotalAmount(), $invoice->contract->currency, true)</span>
</div>
<hr>
<div class="d-flex justify-content-between mb-2">
  <span class="me-2">Subtotal:</span>
  <span class="fw-semibold">@cMoney($invoice->subtotal, $invoice->contract->currency, true)</span>
</div>

<hr>
@forelse ($invoice->taxes as $tax)
@continue($tax->pivot->invoice_item_id != null)
<div class="d-flex justify-content-between mb-2">
  <span class="me-2">{{$tax->name}}:</span>
  <span class="fw-semibold">
    @if($tax->pivot->type != 'Percent')
      @cMoney($tax->pivot->amount, $invoice->contract->currency, true)
    @else
      {{$tax->pivot->amount / 1000}}%
    @endif
  </span>
</div>
@empty
@endforelse
<div class="d-flex justify-content-between">
  <span class="me-2">Total Tax:</span>
  <span class="fw-semibold">
    @if($tab != 'authority-tax')
      @cMoney($invoice->totalAppliedTax(), $invoice->contract->currency, true)
    @else
      @cMoney($invoice->authorityInvoice->total, $invoice->contract->currency, true)
    @endif
  </span>
</div>
<hr />
  <div class="d-flex justify-content-between mb-2">
    <span class="me-2">Down Payment:</span>
    <span class="fw-semibold">@cMoney(-$invoice->totalDeductedAmount(), $invoice->contract->currency, true)</span>
  </div>

@if($invoice->adjustment_amount != 0)
<hr />
  <div class="d-flex justify-content-between mb-2">
    <span class="me-2">{{$invoice->adjustment_description}}:</span>
    <span class="fw-semibold">@cMoney($invoice->adjustment_amount, $invoice->contract->currency, true)</span>
  </div>
@endif
@if($invoice->discount_amount != 0)
<hr />
  <div class="d-flex justify-content-between mb-2">
    <span class="me-2">Discount @if ($invoice->discount_type == 'Percentage')({{$invoice->discount_percentage}}%)@endif</span>
    <span class="fw-semibold"> - @cMoney($invoice->discount_amount, $invoice->contract->currency, true)</span>
  </div>
@endif
<hr />
<div class="d-flex justify-content-between">
  <span class="me-2">Total:</span>
  @if($tab != 'authority-tax')
    <span class="fw-semibold invoice_total" data-amount="{{$invoice->total}}">
      @cMoney($invoice->total, $invoice->contract->currency, true)
    </span>
  @else
    <span class="fw-semibold invoice_total" data-amount="{{$invoice->authorityInvoice->total}}">
      @cMoney($invoice->authorityInvoice->total, $invoice->contract->currency, true)
    </span>
  @endif
</div>
@if($tab == 'summary')
  @if($invoice->rounding_amount != 0)
  <hr />
    <div class="d-flex justify-content-between mb-2">
      <span class="me-2">Rounding:</span>
      <span class="fw-semibold">@cMoney($invoice->rounding_amount, $invoice->contract->currency, true)</span>
    </div>
  @endif
  <div class="d-flex justify-content-between">
    <span class="switch-label mt-2">Round Total</span>
    <label class="col-form-label pe-4">
      <label class="switch">
        {{ Form::checkbox('rounding_amount', 1, $invoice->rounding_amount && 1,['class' => 'switch-input', 'id' => 'invoice-rounding-amount'])}}
        <span class="switch-toggle-slider">
          <span class="switch-on"></span>
          <span class="switch-off"></span>
        </span>
      </label>
    </label>
  </div>
@endif

@if($invoice->downpayment_amount != 0 && $invoice->downPayments()->wherePivot('is_after_tax', 1)->count() > 0)
<hr>
  @forelse ($invoice->downPayments as $dp)
    @if ($dp->pivot->is_after_tax == 0)
      @continue
    @endif
    <div class="d-flex justify-content-between">
      <span class="me-2">DP-{{runtimeInvIdFormat($dp->id) }}
        @if ($dp->pivot->is_percentage)
          ({{$dp->pivot->percentage / 1000}}%)
        @endif
      :</span>
      <span class="fw-semibold">@cMoney(-$dp->pivot->amount / 1000, $invoice->contract->currency, true)</span>
    </div>
  @empty
  @endforelse
@endif

@if($invoice->retention_name != null)
  <hr />
  <div class="d-flex justify-content-between mb-2">
    <span class="me-2">
      <a data-toggle="ajax-delete" data-href="{{route('admin.invoices.destroy-retention', ['invoice' => $invoice->id])}}"><i class="ti ti-trash"></i></a>
      Retention ({{$invoice->retention_name}} @if ($invoice->retention_percentage){{$invoice->retention_percentage}}%@endif):
    </span>
    <span class="fw-semibold"
      @if($invoice->retention_manual_amount)
        data-bs-toggle="tooltip" data-placement="top" title="Calculated Amount : {{cMoney($invoice->getRawOriginal('retention_amount') /1000, $invoice->contract->currency, true)}}"
      @endif
    >
      @cMoney(-$invoice->retention_amount, $invoice->contract->currency, true)
      @if($invoice->retention_manual_amount)
       <span class="text-danger">*</span>
      @endif
    </span>
  </div>
@endif
@if ($tab == 'summary' && $invoice->paid_amount != 0)
  <hr />
  <div class="d-flex justify-content-between">
    <span class="w-px-100">Paid:</span>
    <span class="fw-semibold">@cMoney($invoice->paid_amount, $invoice->contract->currency, true)</span>
  </div>
@elseif($tab == 'authority-tax' && $invoice->authorityInvoice->paid_amount != 0)
  <hr />
  <div class="d-flex justify-content-between">
    <span class="w-px-100">Paid:</span>
    <span class="fw-semibold">@cMoney($invoice->authorityInvoice->paid_amount, $invoice->contract->currency, true)</span>
  </div>
@endif

<hr />
<div class="d-flex justify-content-between">
  <span class="w-px-100">Payable:</span>
  <span class="fw-semibold">@cMoney($tab == 'summary' ? $invoice->payableAmount() : $invoice->authorityInvoice->payableAmount(), $invoice->contract->currency, true)</span>
</div>
