@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

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
{{-- <script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script> --}}
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/admin-roles-permissions.js')}}></script>
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
              @if ($loop->iteration <= 10)
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $user->full_name }}" class="avatar avatar-sm pull-up">
                <img class="rounded-circle" src="{{ $user->avatar }}" alt="Avatar">
              </li>
              @else
                <div class="avatar">
                  <span class="avatar-initial rounded-circle pull-up" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{count($role->users)-$loop->iteration+1}}">+{{count($role->users)-$loop->iteration+1}}</span>
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
              @can('update role')
                <a href="javascript:;" data-role="{{$role->id}}" class="open-role-edit-modal"><span>Edit Role</span></a>
              @endcan
            </div>
            <a href="javascript:void(0);" class="text-muted"><i class="ti ti-copy ti-md"></i></a>
          </div>
        </div>
      </div>
    </div>
    @empty
    @endforelse
  @endcan
  @can('create role')
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100">
        <div class="row h-100">
          <div class="col-sm-5">
            <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-3">
              <img src="{{ asset('assets/img/illustrations/add-new-roles.png') }}" class="img-fluid mt-sm-4 mt-md-0" alt="add-new-roles" width="83">
            </div>
          </div>
          <div class="col-sm-7">
            <div class="card-body text-sm-end text-center ps-sm-0">
              <button onclick="addRole();" class="btn btn-primary mb-2 text-nowrap add-new-role">Add New Role</button>
              <p class="mb-0 mt-1">Add role, if it does not exist</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endcan
</div>
<!--/ Role cards -->
@can('read user')
  <div class="mt-3  col-12">
    <div class="card">
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col-md-4 user_role">
            <select name="filter_companies[]" class="form-select select2" multiple data-placeholder="Select Company">
              @forelse ($partners as $id => $company)
                <option value="{{$id}}"> {{$company}} </option>
              @empty
              @endforelse

            </select>
          </div>
          <div class="col-md-4 user_plan">
            <select name="filter_status[]" class="form-select select2" multiple data-placeholder="User Status">
              @forelse ($statuses as $status)
                <option value="{{$status}}">{{ucfirst($status)}}</option>
              @empty
              @endforelse
              </select>
            </div>
          <div class="col-md-4 user_status">
            <select name="filer_roles[]" class="form-select select2" multiple data-placeholder="User Role">
              @forelse ($roles_filter as $role)
                <option value="{{$role}}">{{$role}}</option>
              @empty
              @endforelse
            </select>
          </div>
        </div>
      </form>
      <div class="card-body">
        {{$dataTable->table()}}
      </div>
    </div>
  </div>
@endcan



<!-- Add Role Modal -->
@include('admin/_partials/_modals/modal-add-role')
@include('admin/_partials/_modals/modal-edit-role')
<!-- / Add Role Modal -->
@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      $(document).ready(function () {
          $('.js-datatable-filter-form :input').on('change', function (e) {
              window.LaravelDataTables["admins-table"].draw();
          });

          $('#admins-table').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
