<div class="d-flex justify-content-between mb-2">
  <span class="w-px-100">Subtotal:</span>
  <span class="fw-semibold">{{$invoice->subtotal}}</span>
</div>
{{-- {{dd($invoice->taxes)}} --}}
@forelse ($invoice->taxes->where('pivot_invoice_item_id', null) as $tax)
<div class="d-flex justify-content-between mb-2">
  <span class="w-px-100">{{$tax->name}}:</span>
  <span class="fw-semibold">{{$tax->pivot->amount}} {{$tax->pivot->type != 'Fixed' ? '%' : ''}}</span>
</div>
@empty
@endforelse
<hr />
<div class="d-flex justify-content-between">
  <span class="w-px-100">Total:</span>
  <span class="fw-semibold">{{$invoice->total}}</span>
</div>
