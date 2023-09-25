<div class="d-flex justify-content-between mb-2">
  <span class="w-px-100">Subtotal:</span>
  <span class="fw-semibold">@money($invoice->subtotal, $invoice->contract->currency, true)</span>
</div>
@forelse ($invoice->taxes as $tax)
@continue($tax->pivot->invoice_item_id != null)
<div class="d-flex justify-content-between mb-2">
  <span class="w-px-100">{{$tax->name}}:</span>
  <span class="fw-semibold">
    @if($tax->pivot->type != 'Percent')
      @money($tax->pivot->amount, $invoice->contract->currency, true)
    @else
      {{$tax->pivot->amount}}%
    @endif
  </span>
</div>
@empty
@endforelse
<div class="d-flex justify-content-between">
  <span class="w-px-100">Total Tax:</span>
  <span class="fw-semibold">@money($invoice->total_tax, $invoice->contract->currency, true)</span>
</div>
<hr />
<div class="d-flex justify-content-between">
  <span class="w-px-100">Total:</span>
  <span class="fw-semibold">@money($invoice->total, $invoice->contract->currency, true)</span>
</div>
