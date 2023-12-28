<div class="d-inline-block text-nowrap">
  {{-- <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.tax-authority-invoices.destroy', $invoice) }}"><i class="ti ti-trash"></i></button> --}}
  @if($invoice->invoice->status == 'Paid' && $invoice->payableAmount() > 0)
    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
      class="ti ti-dots-vertical"></i></button>
    <div class="dropdown-menu dropdown-menu-end m-0">
        <button data-toggle="ajax-modal" data-title="{{__('Add Payment')}}" data-href="{{route('admin.finances.payments.create',['invoice' => $invoice->id, 'type' => 'tax-authority'])}}" class="dropdown-item">{{__('Add Payment')}}</button>
    </div>
  @endif
</div>
