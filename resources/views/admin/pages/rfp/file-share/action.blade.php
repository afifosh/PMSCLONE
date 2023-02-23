<div class="d-inline-block text-nowrap">
    @if ($fileShare->expires_at && $fileShare->expires_at < today() && !$fileShare->revoked_by)
        <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-modal"
            data-href="{{ route('admin.draft-rfps.files.shares.reinvite', ['draft_rfp' => $fileShare->id, 'file' => $fileShare->rfp_file_id, 'share' => $fileShare->id]) }}"
            data-title="Reinvite User"><i
                class="ti ti-refresh"></i></button>
    @endif
    <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
        data-href="{{ route('admin.draft-rfps.files.shares.destroy', ['draft_rfp' => $fileShare->id, 'file' => $fileShare->rfp_file_id, 'share' => $fileShare->id]) }}"><i
            class="ti ti-trash"></i></button>
    @if (!$fileShare->revoked_by)
        <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
                class="ti ti-dots-vertical"></i></button>
        <div class="dropdown-menu dropdown-menu-end m-0">
            <a href="javascript:;" class="dropdown-item" data-toggle="ajax-modal" data-title="Confirmation"
                data-href="{{ route('admin.draft-rfps.files.shares.revoke', ['draft_rfp' => $fileShare->id, 'file' => $fileShare->rfp_file_id, 'share' => $fileShare->id]) }}">Revoke</a>
        </div>
    @endif

</div>
