@can('download submitted form')
    <a href="{{ route('admin.applications.settings.download.form.values.pdf', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Download') }}" class="btn btn-success btn-sm"
        data-toggle="tooltip"><i class="ti ti-file-download"></i></a>
@endcan
@can('read submitted form')
    <a href="{{ route('admin.applications.settings.form-values.show', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Show') }}" title="{{ __('View Survey') }}"
        class="btn btn-info btn-sm" data-toggle="tooltip"><i class="ti ti-eye"></i></a>
@endcan
@can('edit submitted form')
    <a href="{{ route('admin.applications.settings.form-values.edit', $formValue->id) }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
         data-bs-original-title="{{ __('Edit') }}" title="{{ __('Edit Survey') }}"
        class="btn btn-primary btn-sm" data-toggle="tooltip"><i class="ti ti-edit"></i> </a>
@endcan
@can('delete submitted form')
    <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="bottom"
        data-bs-original-title="{{ __('Delete') }}"
        data-href="{{ route('admin.applications.settings.form-values.destroy', $formValue->id)}}"
        data-toggle="ajax-delete"
        ><i class="ti ti-trash"></i></a>
@endcan

