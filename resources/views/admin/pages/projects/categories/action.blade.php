<div class="d-inline-block text-nowrap">
    <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Category" data-href="{{route('admin.project-categories.edit', $category)}}"><i class="ti ti-edit"></i></button>
        <button class="btn btn-sm btn-icon delete-record {{$category->projects_count != 0 ? 'disabled' : ''}}" data-toggle="ajax-delete"
            data-href="{{ route('admin.project-categories.destroy', ['project_category' => $category->id]) }}"><i
                class="ti ti-trash"></i></button>
</div>
