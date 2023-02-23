<div class="d-inline-block text-nowrap">
    @can(true)
        <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
            data-href="{{ route('admin.draft-rfps.files.shares.destroy', ['draft_rfp' => $fileShare->id, 'file' => $fileShare->rfp_file_id, 'share' => $fileShare->id]) }}"><i
                class="ti ti-trash"></i></button>
    @endcan
    @if (!$fileShare->revoked_by)
        <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
                class="ti ti-dots-vertical"></i></button>
        <div class="dropdown-menu dropdown-menu-end m-0">
            <a href="javascript:;" class="dropdown-item" data-toggle="ajax-modal" data-title="Confirmation" data-href="{{ route('admin.draft-rfps.files.shares.revoke', ['draft_rfp' => $fileShare->id, 'file' => $fileShare->rfp_file_id, 'share' => $fileShare->id]) }}">Revoke</a>
        </div>
    @endif

</div>
