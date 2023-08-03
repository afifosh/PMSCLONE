@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Departments')

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
<script src={{asset('assets/js/custom/admin-roles-permissions.js')}}></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')
  <div class="mt-3  col-12">
    <div class="card">
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col-md-4 user_role">
            <select name="filter_organizations[]" class="form-select select2" multiple data-placeholder="Select Organization">
              @forelse ($organizations as $id => $company)
                <option value="{{$id}}"> {{$company}} </option>
              @empty
              @endforelse

            </select>
          </div>
          <div class="col-md-4 user_status">
            <select name="filer_heads[]" class="form-select select2User" multiple data-placeholder="Department Head">
              @forelse ($heads as $head)
                <option value="{{$head->id}}" data-full_name="{{$head->full_name}}" data-avatar="{{$head->avatar}}">{{$head->email}}</option>
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

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      $(document).ready(function () {
          $('.js-datatable-filter-form :input').on('change', function (e) {
              window.LaravelDataTables["{{App\Models\CompanyDepartment::DT_ID}}"].draw();
          });

          $('#{{App\Models\CompanyDepartment::DT_ID}}').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
