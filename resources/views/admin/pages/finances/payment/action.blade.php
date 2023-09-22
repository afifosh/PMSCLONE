<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Tax" data-href="{{route('admin.finances.payments.edit', $invoicePayment)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.finances.payments.destroy', $invoicePayment) }}"><i class="ti ti-trash"></i></button>
</div>
