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
  <div class="mt-3  col-12">
    <div class="card">
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col-md-4">
            {!! Form::select('filter_drafts[]', $drafts, null, ['class' => 'form-select select2', 'multiple', 'data-placeholder' => "Draft"]) !!}
          </div>
          <div class="col-md-4">
            <select name="filter_shared_by[]" class="form-select select2User" multiple data-placeholder="Shared By">
              @forelse ($sharedBy as $user)
                <option value="{{$user->id}}" data-full_name="{{$user->full_name}}" data-avatar="{{$user->avatar}}">{{$user->email}}</option>
              @empty
              @endforelse
            </select>
          </div>
          <div class="col-md-4">
            {!! Form::select('filter_files[]', $files, null, ['class' => 'form-select select2', 'multiple', 'data-placeholder' => "Files"]) !!}
          </div>
          <div class="col-md-4">
            {!! Form::select('filter_permissions[]', $permissions, null, ['class' => 'form-select select2', 'multiple', 'data-placeholder' => "Permission"]) !!}
          </div>
          <div class="col-md-4 mt-3">
            {!! Form::select('filter_status', $statuses, null, ['class' => 'form-select select2']) !!}
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
