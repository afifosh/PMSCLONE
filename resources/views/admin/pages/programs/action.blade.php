<div class="d-inline-block text-nowrap">
        <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Program" data-href="{{route('admin.programs.edit', $program)}}"><i class="ti ti-edit"></i></button>
        <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
            data-href="{{ route('admin.programs.destroy', $program) }}"><i class="ti ti-trash"></i></button>
        <a class="btn btn-sm btn-icon" href="{{ route('admin.programs.show', $program) }}">
                <i class="ti ti-eye"></i>
        </a>           
</div>
