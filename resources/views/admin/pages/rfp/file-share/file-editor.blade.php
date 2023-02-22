@if ($row->file->is_editable())
    <a href="{{ route('admin.edit-file', $row->file) }}">{{ $row->file->title }}</a>
@else
    {{ $row->file->title }}
@endif
