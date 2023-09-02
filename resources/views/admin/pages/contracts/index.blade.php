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
      @if (!isset($project))
        <form class="js-datatable-filter-form">
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
            <div class="col">
              {!! Form::label('projects', 'Projects') !!}
              {!! Form::select('projects[]', $projects, null, ['class' => 'form-select select2', 'data-placeholder' => 'Projects', 'data-dropdownParent' => '$("#gantt-chart-card")']) !!}
            </div>
            <div class="col">
              {!! Form::label('project Status', 'Contract Status') !!}
              {!! Form::select('filter_status', [0 => 'All'] + $contract_statuses, null, ['class' => 'form-select select2', 'data-placeholder' => 'Status']) !!}
            </div>
            <div class="col">
              {!! Form::label('contract_type', 'Contract Type') !!}
              {!! Form::select('contract_type', $contractTypes, null, ['class' => 'form-select select2', 'data-placeholder' => 'Type']) !!}
            </div>
            <div class="col">
              {!! Form::label('assigned_to_type', 'Assigned To') !!}
              {!! Form::select('assigned_to_type', ['Both' => 'Both', 'Client' => 'Client', 'Company' => 'Company'], null, ['class' => 'form-select select2', 'data-placeholder' => 'Assigned To']) !!}
            </div>
            <div class="col d-none">
              {!! Form::label('companies', 'Company') !!}
              {!! Form::select('companies', $companies, null, ['class' => 'form-select select2', 'data-placeholder' => 'Company']) !!}
            </div>
            <div class="col d-none">
              {!! Form::label('clients', 'Clients') !!}
              {!! Form::select('contract_client', $contractClients, null, ['class' => 'form-select select2', 'data-placeholder' => 'Clients']) !!}
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
    <script>
      $(document).ready(function () {
          $(document).on('change', '[name="assigned_to_type"]', function(e){
            if($(this).val() == 'Company'){
              $('[name="companies"]').closest('.col').removeClass('d-none');
              $('[name="contract_client"]').closest('.col').addClass('d-none');
              $('[name="companies"]').val('0').trigger('change');
              $('[name="contract_client"]').val('0').trigger('change');
            }else if($(this).val() == 'Client'){
              $('[name="companies"]').closest('.col').addClass('d-none');
              $('[name="contract_client"]').closest('.col').removeClass('d-none');
              $('[name="companies"]').val('0').trigger('change');
              $('[name="contract_client"]').val('0').trigger('change');
            }else{
              $('[name="companies"]').closest('.col').addClass('d-none');
              $('[name="contract_client"]').closest('.col').addClass('d-none');
              $('[name="companies"]').val('0').trigger('change');
              $('[name="contract_client"]').val('0').trigger('change');
            }
          })
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
