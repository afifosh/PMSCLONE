<div class="d-flex justify-content-between mb-2">
  <span class="me-2">Subtotal:</span>
  <span class="fw-semibold">@cMoney($invoice->subtotal, $invoice->contract->currency, true)</span>
</div>

@if($invoice->discount_amount != 0)
  <div class="d-flex justify-content-between mb-2">
    <span class="me-2">Discount @if ($invoice->discount_type == 'Percentage')({{$invoice->discount_percentage}}%)@endif</span>
    <span class="fw-semibold">@cMoney($invoice->discount_amount, $invoice->contract->currency, true)</span>
  </div>
@endif

<hr>
@forelse ($invoice->taxes as $tax)
@continue($tax->pivot->invoice_item_id != null)
<div class="d-flex justify-content-between mb-2">
  <span class="me-2">{{$tax->name}}:</span>
  <span class="fw-semibold">
    @if($tax->pivot->type != 'Percent')
      @cMoney($tax->pivot->amount, $invoice->contract->currency, true)
    @else
      {{$tax->pivot->amount}}%
    @endif
  </span>
</div>
@empty
@endforelse
<div class="d-flex justify-content-between">
  <span class="me-2">Total Tax:</span>
  <span class="fw-semibold">@cMoney($invoice->total_tax, $invoice->contract->currency, true)</span>
</div>
@if($invoice->adjustment_amount != 0)
<hr />
  <div class="d-flex justify-content-between mb-2">
    <span class="me-2">{{$invoice->adjustment_description}}:</span>
    <span class="fw-semibold">@cMoney($invoice->adjustment_amount, $invoice->contract->currency, true)</span>
  </div>
@endif
<hr />
<div class="d-flex justify-content-between">
  <span class="me-2">Total:</span>
  <span class="fw-semibold">@cMoney($invoice->total, $invoice->contract->currency, true)</span>
</div>

@if($invoice->retention_name != null)
  <hr />
  <div class="d-flex justify-content-between mb-2">
    <span class="me-2">Retention ({{$invoice->retention_name}} @if ($invoice->retention_percentage){{$invoice->retention_percentage}}%@endif):</span>
    <span class="fw-semibold">@cMoney($invoice->retention_amount, $invoice->contract->currency, true)</span>
  </div>
@endif
@if ($invoice->paid_amount)
  <hr />
  <div class="d-flex justify-content-between">
    <span class="w-px-100">Paid:</span>
    <span class="fw-semibold">@cMoney($invoice->paid_amount, $invoice->contract->currency, true)</span>
  </div>
@endif

<hr />
<div class="d-flex justify-content-between">
  <span class="w-px-100">Payable:</span>
  <span class="fw-semibold">@cMoney($invoice->total - $invoice->paid_amount - $invoice->retention_amount, $invoice->contract->currency, true)</span>
</div>
