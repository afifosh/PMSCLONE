<div class="d-inline-block text-nowrap">
    <button class="btn btn-sm btn-icon" data-title={{__('Edit Artwork')}} data-toggle="ajax-modal" data-href="{{ route('admin.artworks.edit', $artwork) }}"><i class="ti ti-edit"></i></button>
    <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
        data-href="{{ route('admin.artworks.destroy', $artwork) }}"><i class="ti ti-trash"></i></button>
    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
            class="ti ti-dots-vertical"></i></button>
    <div class="dropdown-menu dropdown-menu-end m-0">
        <a data-href="{{ route('admin.artworks.edit', $artwork) }}" class="dropdown-item">{{ __('View') }}</a>
    </div>
</div>
