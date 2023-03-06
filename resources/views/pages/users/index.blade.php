@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Roles - Apps')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/company-show-role.js')}}></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')
<h4 class="fw-semibold mb-4">Roles List</h4>
<p class="mb-4">A role provided access to predefined menus and features so that depending on <br> assigned role an administrator can have access to what user needs.</p>
<!-- Role cards -->
<div class="row g-4">
  @can('read role')
    @forelse ($roles as $role)
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h6 class="fw-normal mb-2">Total {{ $role->users_count}} users</h6>
            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
              @forelse ($role->users as $user)
                @if ($loop->iteration <= 5)
                <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $user->full_name }}" class="avatar avatar-sm pull-up">
                  <img class="rounded-circle" src="{{ $user->avatar }}" alt="Avatar">
                </li>
                @else
                  <div class="avatar">
                    <span class="avatar-initial bg-dark text-light rounded-circle pull-up" data-bs-toggle="tooltip" data-bs-placement="top" title="+{{count($role->users)-$loop->iteration+1}}">+{{count($role->users)-$loop->iteration+1}}</span>
                  </div>
                @break
                @endif
              @empty
              @endforelse
            </ul>
          </div>
          <div class="d-flex justify-content-between align-items-end mt-1">
            <div class="role-heading">
              <h4 class="mb-1">{{$role->name}}</h4>
                <a href="javascript:;" data-role="{{$role->id}}" class="open-show-role-modal"><span>View Permissions</span></a>
            </div>
            <a href="javascript:void(0);" class="text-muted"><i class="ti ti-copy ti-md"></i></a>
          </div>
        </div>
      </div>
    </div>
    @empty
    @endforelse
  @endcan
</div>
<!--/ Role cards -->
@can('read user')
  <div class="mt-3  col-12">
    <div class="card">
      <div class="card-body">
        {{$dataTable->table()}}
      </div>
    </div>
  </div>
@endcan



<!-- Add Role Modal -->
@include('_partials/_modals/modal-show-role')
<!-- / Add Role Modal -->
@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
@endpush
