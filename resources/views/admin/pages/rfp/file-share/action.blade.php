<div class="d-inline-block text-nowrap">
  @can(true)
      <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
          data-href="{{ route('admin.draft-rfps.files.shares.destroy', ['draft_rfp' => $fileShare->id, 'file' => $fileShare->rfp_file_id, 'share' => $fileShare->id]) }}"><i class="ti ti-trash"></i></button>
  @endcan
</div>
