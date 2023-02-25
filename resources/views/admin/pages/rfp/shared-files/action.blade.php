<div class="d-inline-block text-nowrap">
    <a class="btn btn-sm btn-icon" target="_blank"
        href="{{ route('admin.draft-rfps.files.show', ['draft_rfp' => $fileShare->id, 'file' => $fileShare->rfp_file_id]) }}"><i class="fa fa-lg fa-light fa-eye"></i></a>
    @if ($fileShare->file->is_editable())
      <a class="btn btn-sm btn-icon" target="_blank"
          href="{{ route('admin.edit-file', ['file' => $fileShare->rfp_file_id]) }}"><i class="ti ti-pencil"></i></a>
    @endif
    <a class="btn btn-sm btn-icon"
        href="{{ route('admin.shared-files.file-activity', ['file' => $fileShare->rfp_file_id]) }}"><i class="fa fa-lg fa-list-check"></i></a>
    <a class="btn btn-sm btn-icon"
        href="{{ route('admin.shared-files.file-versions', ['file' => $fileShare->rfp_file_id]) }}"><i class="fa fa-lg fa-clock-rotate-left"></i></a>
</div>
