<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.contracts.notifiable-users.destroy', ['contract' => $contract->id, 'notifiable_user' => $admin->id]) }}"><i class="ti ti-trash"></i></button>
</div>
