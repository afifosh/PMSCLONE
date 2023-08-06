@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Designations')

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
<style>
  .dt-bootstrap5 .dropdown-menu {
    position: relative;
    float: none;
    width: 160px;
}
</style>
  <div class="mt-3  col-12">
    <div class="card">
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col-md-4 user_role">
            <select name="filter_organizations[]" class="form-select select2" multiple data-placeholder="Select Organization">
              @forelse ($organizations as $id => $organization)
                <option value="{{$id}}"> {{$organization}} </option>
              @empty
              @endforelse

            </select>
          </div>
          <div class="col-md-4 user_status">
            <select name="filer_departments[]" class="form-select select2" multiple data-placeholder="Select Department">
              @forelse ($departments as $id => $department)
                <option value="{{$id}}">{{$department}}</option>
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
              window.LaravelDataTables["{{App\Models\CompanyDesignation::DT_ID}}"].draw();
          });

          $('#{{App\Models\CompanyDesignation::DT_ID}}').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
