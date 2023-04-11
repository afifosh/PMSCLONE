@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', $title)

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')
<h4 class="fw-semibold mb-4">{{__($title)}}</h4>

<div class="mt-3  col-12">
  <div class="card">
    @if ($type == 'change' || $type == 'approval')
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          @isset($levels)
            <div class="col-md-12 levels">
              <select name="filter_levels[]" class="form-select select2" multiple data-placeholder="Filter By Level">
                @forelse ($levels as $id => $level)
                  <option value="{{$loop->iteration}}"> Level {{$loop->iteration}} ( {{$level}} ) </option>
                @empty
                @endforelse
              </select>
            </div>
          @endisset
        </div>
      </form>
    @endif
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
              window.LaravelDataTables["{{App\Models\Company::DT_ID}}"].draw();
          });

          $('#{{App\Models\Company::DT_ID}}').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
