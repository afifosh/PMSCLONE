<div class="d-inline-block text-nowrap">
    @can(true)
        <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
            data-href="{{ route('admin.company-invitations.destroy', $invitation) }}"><i class="ti ti-trash"></i></button>
    @endcan
    @if ($invitation->status != 'accepted')
      <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
              class="ti ti-dots-vertical"></i></button>
      <div class="dropdown-menu dropdown-menu-end m-0">
            <a href="javascript:;" data-toggle="ajax-modal" data-title="Resend Invitation" data-href="{{route('admin.company-invitations.edit', $invitation)}}" class="dropdown-item">Resend</a>
          @if ($invitation->status != 'revoked' && $invitation->status != 'accepted')
            <a href="javascript:;" data-toggle="ajax-modal" data-title="Revoke Confirmation" data-href="{{route('admin.company-invitations.revoke', $invitation)}}" class="dropdown-item">Revoke</a>
          @endif
      </div>
    @endif
</div>
