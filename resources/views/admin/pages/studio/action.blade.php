<div class="d-inline-block text-nowrap">
    @can('update company')
    <a class="text-body" href="{{route('admin.studios.edit', $studio)}}"><i class="ti ti-edit"></i></a>
    @endcan
    @can('delete company')
        <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
            data-href="{{ route('admin.studios.destroy', $studio) }}"><i class="ti ti-trash"></i></button>
    @endcan
    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
            class="ti ti-dots-vertical"></i></button>
    <div class="dropdown-menu dropdown-menu-end m-0">
        <a data-href="{{route('admin.studios.edit', $studio)}}" class="dropdown-item">{{__('View')}}</a>
        @can('update company')
            <a href="javascript:;" class="dropdown-item">{{__('Suspend')}}</a>
        @endcan
    </div>
</div>
{{-- <span>
    @if ($admin->active_status != 1)
        <a class="btn btn-icon btn-danger btn-sm d-inline" href="account-status/{{ $admin->id }}" id="edit-user"
            data-action="users/{{ $admin->id }}/edit"><i class="ti ti-user-off"></i></a>
    @else
        <a class="btn btn-icon btn-success btn-sm d-inline" href="account-status/{{ $admin->id }}"
            data-action="users/{{ $admin->id }}/edit"><i class="ti ti-user-check"></i></a>
    @endif
    @can('edit user')
        <a class="btn btn-icon btn-primary btn-sm d-inline" href="javascript:void(0);" id="edit-user"
            data-action="users/{{ $admin->id }}/edit"><i class="ti ti-edit"></i></a>
    @endcan
    @can('delete user')
    {!! Form::open(['method' => 'DELETE', 'route' => ['admin.roles.destroy', $admin->id], 'id' => 'delete-form-' . $admin->id,'class'=>'d-inline']) !!}
        <a href="#" class="btn btn-sm small btn-danger show_confirm" id="delete-form-{{ $admin->id }}"><i
            class="ti ti-trash mr-0"></i></a>
        {!! Form::close() !!}
    @endcan
</span> --}}
