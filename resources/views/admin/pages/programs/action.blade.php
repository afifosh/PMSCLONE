<div class="d-inline-block text-nowrap">
    <a class="btn btn-sm btn-icon" data-bs-toggle="tooltip" aria-label="Preview" data-bs-original-title="Preview" href="{{route('admin.programs.show', $program)}}"><i
        class="ti ti-eye"></i></a>    
    @can(true)
        <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Program" data-href="{{route('admin.programs.edit', $program)}}"><i class="ti ti-edit"></i></button>
        <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
            data-href="{{ route('admin.programs.destroy', $program) }}"><i class="ti ti-trash"></i></button>
    @endcan
</div>
