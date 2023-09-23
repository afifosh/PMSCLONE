<div class="d-flex justify-content-between mb-2">
  <span class="w-px-100">Subtotal:</span>
  <span class="fw-semibold">{{$invoice->subtotal}}</span>
</div>
@forelse ($invoice->taxes as $tax)
@continue($tax->pivot->invoice_item_id != null)
<div class="d-flex justify-content-between mb-2">
  <span class="w-px-100">{{$tax->name}}:</span>
  <span class="fw-semibold">{{$tax->pivot->amount}} {{$tax->pivot->type != 'Fixed' ? '%' : ''}}</span>
</div>
@empty
@endforelse
<div class="d-flex justify-content-between">
  <span class="w-px-100">Total Tax:</span>
  <span class="fw-semibold">{{$invoice->total_tax}}</span>
</div>
<hr />
<div class="d-flex justify-content-between">
  <span class="w-px-100">Total:</span>
  <span class="fw-semibold">{{$invoice->total}}</span>
</div>
