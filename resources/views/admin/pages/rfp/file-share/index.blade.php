@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'RFPs')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection

@section('content')
@include('admin.pages.rfp.header', ['tab' => 'shared-files'])
@can(true)
  <div class="mt-3  col-12">
    <div class="card">
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col-md-4">
            <select name="filter_shared_by[]" class="form-select select2User" multiple data-placeholder="Shared By">
              @forelse ($sharedBy as $user)
                <option value="{{$user->id}}" data-full_name="{{$user->full_name}}" data-avatar="{{$user->avatar}}">{{$user->email}}</option>
              @empty
              @endforelse
            </select>
          </div>
          <div class="col-md-4">
            <select name="filter_shared_with[]" class="form-select select2User" multiple data-placeholder="Shared With">
              @forelse ($sharedWith as $user)
                <option value="{{$user->id}}" data-full_name="{{$user->full_name}}" data-avatar="{{$user->avatar}}">{{$user->email}}</option>
              @empty
              @endforelse
            </select>
          </div>
          <div class="col-md-4">
            <select name="filter_files[]" class="form-select select2" multiple data-placeholder="Files">
              @forelse ($files as $id => $file)
                <option value="{{$id}}">{{ $file }}</option>
              @empty
              @endforelse
            </select>
          </div>
          <div class="col-md-4 mt-3">
            <select name="filter_permissions[]" class="form-select select2" multiple data-placeholder="Permission">
              @forelse ($permissions as $id => $permission)
                <option value="{{$id}}">{{ $permission }}</option>
              @empty
              @endforelse
            </select>
          </div>
          <div class="col-md-4 mt-3">
            <select name="filter_status" class="form-select select2">
              <option value="">@lang('Select Status')</option>
              @forelse ($statuses as $id => $status)
                <option value="{{$id}}">{{ $status }}</option>
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

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      $(document).ready(function () {
          $('.js-datatable-filter-form :input').on('change', function (e) {
              window.LaravelDataTables["sharedfiles-table"].draw();
          });

          $('#sharedfiles-table').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
