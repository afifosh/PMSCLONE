<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-href="{{route('admin.finances.program-accounts.edit', [$programAccount])}}" data-toggle="ajax-modal" data-title="Edit Account"><i class="ti ti-edit"></i></button>
  @if ($programAccount->balance == 0)
    <button class="btn btn-sm btn-icon delete-record" data-href="{{route('admin.finances.program-accounts.destroy', [$programAccount])}}" data-toggle="ajax-delete"><i class="ti ti-trash"></i></button>
  @endif
</div>
