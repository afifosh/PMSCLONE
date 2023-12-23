<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Category" data-href="{{route('admin.applications.settings.categories.edit', $category)}}"><i class="ti ti-edit"></i></button>
      <button class="btn btn-sm btn-icon delete-record {{$category->applications_count != 0 ? 'disabled' : ''}}" data-toggle="ajax-delete"
          data-href="{{ route('admin.applications.settings.categories.destroy', ['category' => $category->id]) }}"><i
              class="ti ti-trash"></i></button>
</div>
