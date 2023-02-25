@if ($row->file->is_editable())
    <a href="{{ route('admin.edit-file',[ 'file' => $row->file_id, 'rfp' => $$row->file->rfp_id]) }}">{{ $row->file->title }}</a>
@else
    {{ $row->file->title }}
@endif
