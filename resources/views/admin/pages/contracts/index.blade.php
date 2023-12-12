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
@livewireStyles
<x-comments::styles />
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
@includeWhen(isset($company), 'admin.pages.company.header', ['tab' => 'contracts'])
@includeWhen(isset($program), 'admin.pages.programs.program-nav', ['tab' => 'contracts'])
@if (!isset($project) && !isset($company) && !isset($program))
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
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0">{{$contracts['not_started']}}</h5>
                <small>{{__('Not Started')}}</small>
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
                <h5 class="mb-0">{{$contracts['trashed']}}</h5>
                <small>{{__('Trashed')}}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- Stats End --}}
@endif
    <div class="card mt-3">
      @if (!isset($project) && !isset($company) && !isset($program))
        <h5 class="card-header">Search Filter</h5>
        <form class="js-datatable-filter-form">
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
            <div class="col">
              {!! Form::label('projects', 'Projects') !!}
              {!! Form::select('projects', [], [], [
                'class' => 'form-select select2Remote',
                'data-placeholder' => __('All'),
                'data-allow-clear' => 'true',
                'data-url' => route('resource-select', ['Project', 'hasContract'])
              ]) !!}
            </div>
            <div class="col">
              {!! Form::label('programs', 'Programs') !!}
              {!! Form::select('programs', [], [], [
                'class' => 'form-select select2Remote',
                'data-placeholder' => __('All'),
                'data-allow-clear' => 'true',
                'data-url' => route('resource-select', ['Program', 'hasContract'])
              ]) !!}
            </div>
            <div class="col">
              {!! Form::label('project Status', 'Contract Status') !!}
              {!! Form::select('filter_status', $contract_statuses, null, ['class' => 'form-select select2', 'data-placeholder' => 'All', 'data-allow-clear' => 'true']) !!}
            </div>
            <div class="col">
              {!! Form::label('contract_type', 'Contract Type') !!}
              {!! Form::select('contract_type', $contractTypes, null, ['class' => 'form-select select2', 'data-placeholder' => 'All', 'data-allow-clear' => 'true']) !!}
            </div>
            <div class="col">
              {!! Form::label('companies', 'Client') !!}
              {!! Form::select('companies', [], null, [
                'class' => 'form-select select2Remote',
                'data-placeholder' => 'All Clients',
                'data-allow-clear' => 'true',
                'data-url' => route('resource-select', ['groupedCompany', 'hasContract']),
                ]) !!}
            </div>
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
    {{-- Real Time Editing Scripts --}}
    <script>
      /***
       * Variables used in this file
       **/
      var activeContractId = "";
      var activeContractTab = "";
      var contractViewingUsers = [];
      var stageEditingUsers = [];
      var phaseEditingUsers = [];
      var disablePhaseWhisper = false;
      var contractEditingUsers = [];
      var disableContractWhisper = false;
      window.oURL = window.location.href;
    </script>
    <script src="{{asset('assets/js/custom/contracts-realtime-updates.js')}}"></script>
    {{-- End Real Time Editing Scripts --}}
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
    <script>
      window.oURL = window.location.href;
    </script>
    @livewireScripts
    <x-comments::scripts />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
