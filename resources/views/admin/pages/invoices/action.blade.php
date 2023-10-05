<div class="d-inline-block text-nowrap">
  <a class="btn btn-sm btn-icon {{$invoice->isEditable() ?: 'disabled'}}" href="{{route('admin.invoices.edit', $invoice)}}"><i class="ti ti-edit"></i></a>
  <button class="btn btn-sm btn-icon delete-record {{$invoice->isEditable() ?: 'disabled'}}" data-toggle="ajax-delete"
      data-href="{{ route('admin.invoices.destroy', $invoice) }}"><i class="ti ti-trash"></i></button>
</div>
