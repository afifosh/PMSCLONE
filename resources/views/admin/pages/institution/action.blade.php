<div class="d-inline-block text-nowrap">
    @can('update company')
    <button class="btn btn-sm btn-icon" data-title={{__('Edit Institution')}} data-toggle="ajax-modal" data-href="{{ route('admin.institutions.edit', $institution) }}"><i class="ti ti-edit"></i></button>
    @endcan
    @can('delete company')
        <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
            data-href="{{ route('admin.institutions.destroy', $institution) }}"><i class="ti ti-trash"></i></button>
    @endcan
    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
            class="ti ti-dots-vertical"></i></button>
    <div class="dropdown-menu dropdown-menu-end m-0">
        <a data-href="{{route('admin.institutions.edit', $institution)}}" class="dropdown-item">{{__('View')}}</a>
        @can('update company')
            <a href="javascript:;" class="dropdown-item">{{__('Suspend')}}</a>
        @endcan
    </div>
</div>