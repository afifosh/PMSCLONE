<div class="d-inline-block text-nowrap">
  <a class="btn btn-sm btn-icon {{$invoice->isEditable() ?: 'disabled'}}" href="{{route('admin.invoices.edit', $invoice)}}"><i class="ti ti-edit"></i></a>
  <button class="btn btn-sm btn-icon delete-record {{$invoice->isEditable() ?: 'disabled'}}" data-toggle="ajax-delete"
      data-href="{{ route('admin.invoices.destroy', $invoice) }}"><i class="ti ti-trash"></i></button>

  @if($invoice->retention_amount > 0 && !$invoice->retention_released_at)
    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
      class="ti ti-dots-vertical"></i></button>
    <div class="dropdown-menu dropdown-menu-end m-0">
      <a href="javascript:;" data-toggle="confirm-action" data-href="{{route('admin.invoices.release-retention', [$invoice])}}" class="dropdown-item">{{__('Release Retention')}}</a>
    </div>
  @endif
</div>
