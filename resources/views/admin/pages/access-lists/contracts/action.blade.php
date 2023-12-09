<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Contract Access Rule" data-href="{{route('admin.admin-access-lists.contracts.edit', ['admin_access_list' => $admin_id, 'contract' => $contract->id]) }}"><i class="ti ti-edit"></i></button>

  <button class="btn btn-sm btn-icon delete-record {{isset($contract->directACLRules[0]) ?: 'disabled'}}" data-toggle="ajax-delete"
      data-href="{{ route('admin.admin-access-lists.contracts.destroy', ['admin_access_list' => $admin_id, 'contract' => $contract->id]) }}"><i
          class="ti ti-trash"></i></button>

  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0">
      <button class="dropdown-item" onclick="revokeContractAccess({{$admin_id}}, {{$contract->id}})">{{__('Revoke Access')}}</button>
  </div>
</div>
