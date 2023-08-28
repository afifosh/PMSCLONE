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
@if(!isset($project))
  <script>
    window.contractsByType = {!! json_encode($contractTypes); !!};
    window.contractsByValue = {!! json_encode($contractTypesValue) !!};
    window.companiesByProjects = {!! json_encode($companiesByProjects) !!};
    const topContractsByValue = {!! json_encode($contractsByValue) !!};
    const contractsByCycleTime = {!! json_encode($contractsByCycleTime) !!};
    const contractsByExpiryTime = {!! json_encode($contractsByExpiryTime) !!};
    const contractsByStatus = {!! json_encode($contractsByStatus) !!}
    const contractsByDistribution = {!! json_encode($contractsByDistribution) !!}
  </script>
  <script src="{{asset('assets/js/custom/admin-contracts-index.js')}}"></script>
  <script src="{{asset('assets/js/dashboards-crm.js')}}"></script>
  <script>
  </script>
@endif
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
          <div class="col-md-2 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-primary me-3 p-2"><i class="ti ti-chart-pie-2 ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['total']}}</h5>
                <small>{{__('Total')}}</small>
              </div>
            </div>
          </div>
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
                <h5 class="mb-0">{{$contracts['paused']}}</h5>
                <small>{{__('Paused')}}</small>
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

    {{-- Companies By Projects --}}
    <div class="col-12 col-xl-12 mt-2">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Companies By No. Of Contracts</h5>
        </div>
        <div class="card-body row g-3">
          <div class="col-md-6">
            <div id="comapaniesByProjectsChart"></div>
          </div>
          <div class="col-md-6 d-flex justify-content-around align-items-center">
            @forelse ($companiesByProjects as $compByProject)
            @continue($compByProject['percentage'] == 0)
              <div class="d-flex align-items-baseline">
                <span class="text-primary me-2"><i class='ti ti-circle-filled fs-6'></i></span>
                <div>
                  <p class="mb-2">{{$compByProject['name']}}</p>
                  <h5>{{$compByProject['percentage']}}%</h5>
                </div>
              </div>
            @empty
            @endforelse
          </div>
        </div>
      </div>
    </div>
    {{-- Companies By Projects End--}}

    {{-- Types graph start --}}
    <div class="row">
      <div class="col-12 col-xl-6 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">{{__('Contracts by type')}}</h5>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="contracts-by-type"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-6 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">{{__('Contracts Value by type')}}</h5>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="contracts-by-value"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Contracts By Amount --}}
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="card-title m-0 me-2">{{__('Conracts By Amount')}}</h5>
            <small class="text-muted">Top 5 Contracts By Amount ( {{config('app.currency')}} )</small>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="topContractsByValue"></div>
            </div>
          </div>
        </div>
      </div>
      {{-- End Contract By Amount --}}

      {{-- Contracts By Cycle --}}
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">{{__('Conracts By Cycle Time')}}</h5>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="contractsByCycleTime"></div>
            </div>
          </div>
        </div>
      </div>
      {{-- End Contracts By Cycle --}}

      {{-- Contracts By Expiry Time --}}
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">{{__('Conracts By Expiry Time')}}</h5>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-12">
              <div id="contractsByExpiryTime"></div>
            </div>
          </div>
        </div>
      </div>
      {{-- End Contracts By Expiry Time --}}
    </div>


    <div class="row">
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Contracts By Status</h5>
            </div>
          </div>
          <div class="card-body">
            <div id="contractsByStatus"></div>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-4 mt-2">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between">
            <div class="card-title mb-0">
              <h5 class="m-0 me-2">Contracts By Distribution</h5>
            </div>
          </div>
          <div class="card-body">
            <div id="contractsByDistribution"></div>
          </div>
        </div>
      </div>
    </div>
    {{-- Types Graph End --}}
@endif
    <div class="card mt-3">
      <div class="card-body">
        {{$dataTable->table()}}
      </div>
    </div>
  </div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
@endpush
