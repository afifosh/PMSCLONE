@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Contracts')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection

@section('content')
@includeWhen(isset($project), 'admin.pages.projects.navbar', ['tab' => 'contracts'])
@if (!isset($project))
  <div class="mt-3  col-12">
    {{-- Stats Start --}}
    <div class="card h-100">
      <div class="card-header">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">{{__('Contract Summary')}}</h5>
        </div>
      </div>
      <div class="card-body">
        <div class="row gy-3 d-md-flex justify-content-between">
          {{-- <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-primary me-3 p-2"><i class="ti ti-chart-pie-2 ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['total']}}</h5>
                <small>{{__('Total')}}</small>
              </div>
            </div>
          </div> --}}
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-info me-3 p-2"><i class="ti ti-users ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['active']}}</h5>
                <small>{{__('Active')}}</small>
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['paused']}}</h5>
                <small>{{__('Paused')}}</small>
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-danger me-3 p-2"><i class="ti ti-shopping-cart ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['expired']}}</h5>
                <small>{{__('Expired')}}</small>
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['terminateed']}}</h5>
                <small>{{__('Terminated')}}</small>
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['draft']}}</h5>
                <small>{{__('Draft')}}</small>
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['trashed']}}</h5>
                <small>{{__('Trashed')}}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- Stats End --}}

    {{-- Stats Start --}}
    <div class="card h-100 mt-2">
      <div class="card-header">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">{{__('Contract Summary')}}</h5>
        </div>
      </div>
      <div class="card-body">
        <div class="row gy-3 d-md-flex justify-content-between">
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-danger me-3 p-2"><i class="ti ti-shopping-cart ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['expiring_soon']}}</h5>
                <small>{{__('About to Expire')}}</small>
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['recently_added']}}</h5>
                <small>{{__('Recently Added')}}</small>
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['rescheduled']}}</h5>
                <small>{{__('Rescheduled')}}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- Stats End --}}
@endif
    <div class="card mt-3">
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          @isset($contract_statuses)
            <div class="col-md-4 user_role">
              <select name="filter_status" class="form-select select2" data-placeholder="Select Status">
                @forelse ($contract_statuses as $i => $status)
                  <option value="{{$i}}"> {{$status}} </option>
                @empty
                @endforelse
              </select>
            </div>
          @endisset
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
              window.LaravelDataTables["contracts-table"].draw();
          });

          $('#contracts-table').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
