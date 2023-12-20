<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon {{$invoicePayment->type != 1 ?: 'disabled'}}" data-toggle="ajax-modal" data-title="Edit Payment" data-href="{{route('admin.finances.payments.edit', $invoicePayment)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.finances.payments.destroy', $invoicePayment) }}"
      @if($invoicePayment->type == 0 && $invoicePayment->payable->type != 'Down Payment')
        data-warning-des = {{__("Deleting Main Invoice Payment will also delete the authority invoice payments!")}}
      @endif
      ><i class="ti ti-trash"></i></button>
</div>
