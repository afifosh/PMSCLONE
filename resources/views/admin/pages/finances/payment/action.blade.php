<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon {{$invoicePayment->type != 1 ?: 'disabled'}}" data-toggle="ajax-modal" data-title="Edit Payment" data-href="{{route('admin.finances.payments.edit', $invoicePayment)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.finances.payments.destroy', $invoicePayment) }}"><i class="ti ti-trash"></i></button>
</div>
