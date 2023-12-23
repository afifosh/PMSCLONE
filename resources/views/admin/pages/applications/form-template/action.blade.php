@can('desig form template')
    <a class="btn btn-info btn-sm" href="{{ route('admin.applications.settings.formTemplate.design', $FormTemplate->id) }}" id="design-form"
        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Design') }}"><i
            class="ti ti-brush"></i></a>
@endcan
@can('edit form template')
    <a class="btn btn-sm small btn-primary" href="{{ route('admin.applications.settings.form-templates.edit', $FormTemplate->id) }}" data-bs-toggle="tooltip"
        data-bs-placement="bottom" data-bs-original-title="{{ __('Edit') }}" aria-label="{{ __('Edit') }}"><i
            class="ti ti-edit"></i></a>
@endcan
@can('delete form template')
    <a href="#" class="btn btn-sm small btn-danger" data-bs-toggle="tooltip" data-bs-placement="bottom"
        id="delete-form-{{ $FormTemplate->id }}" data-bs-original-title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}"><i
            class="ti ti-trash" data-toggle="ajax-delete" data-href="{{route('admin.applications.settings.form-templates.destroy', $FormTemplate->id)}}"></i></a>
@endcan
