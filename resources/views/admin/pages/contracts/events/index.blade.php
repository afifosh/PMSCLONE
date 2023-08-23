@extends('admin/layouts/layoutMaster')

@section('title', 'Contract Settings')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}">
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection
@section('content')
@include('admin.pages.contracts.header', ['tab' => 'events'])
<div class="mt-3  col-12">
  {{-- Stats Start --}}
  <div class="card h-100">
    <div class="card-header">
      <div class="d-flex justify-content-between mb-3">
        <h5 class="card-title mb-0">{{__('Events Summary')}}</h5>
      </div>
    </div>
    <div class="card-body">
      <div class="row gy-3 d-md-flex justify-content-between">
        <div class="col-md-2 col-6">
          <div class="d-flex align-items-center">
            <div class="badge rounded-pill bg-label-primary me-3 p-2"><i class="ti ti-chart-pie-2 ti-sm"></i></div>
            <div class="card-info">
              <h5 class="mb-0">{{$summary->where('event_type', 'Paused')->sum('total')}}</h5>
              <small>{{__('Paused')}}</small>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-6">
          <div class="d-flex align-items-center">
            <div class="badge rounded-pill bg-label-info me-3 p-2"><i class="ti ti-users ti-sm"></i></div>
            <div class="card-info">
              <h5 class="mb-0">{{$summary->whereIn('event_type', ['Extended', 'Shortened', 'Rescheduled', 'Rescheduled And Amount Increased', 'Rescheduled And Amount Decreased'])->sum('total')}}</h5>
              <small>{{__('Rescheduled')}}</small>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-6">
          <div class="d-flex align-items-center">
            <div class="badge rounded-pill bg-label-danger me-3 p-2"><i class="ti ti-shopping-cart ti-sm"></i></div>
            <div class="card-info">
              <h5 class="mb-0">{{$summary->whereIn('event_type', ['Amount Increased', 'Amount Decreased', 'Rescheduled And Amount Increased', 'Rescheduled And Amount Decreased'])->sum('total')}}</h5>
              <small>{{__('Value Updated')}}</small>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-6">
          <div class="d-flex align-items-center">
            <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
            <div class="card-info">
              <h5 class="mb-0">{{$summary->where('event_type', 'Terminated')->sum('total')}}</h5>
              <small>{{__('Terminated')}}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- Stats End --}}
  <div class="card mt-2">
    <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          <div class="col-md-4">
            <label class="form-label">Select Actioner</label>
            <select name="filter_actioners[]" class="form-select select2" multiple data-placeholder="Select Actioner">
              @forelse ($actioners as $id => $actioner)
                <option value="{{$id}}"> {{$actioner}} </option>
              @empty
              @endforelse

            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Select Type</label>
            <select name="filter_event_types[]" class="form-select select2" multiple data-placeholder="Event Type">
              @forelse ($event_types as $event_type)
                <option value="{{$event_type}}">{{$event_type}}</option>
              @empty
              @endforelse
              </select>
            </div>
        </div>
      </form>
    <div class="card-body">
      <div class="row">
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
            window.LaravelDataTables["events-table"].draw();
        });

        $('#events-table').on('preXhr.dt', function ( e, settings, data ) {
            $('.js-datatable-filter-form :input').each(function () {
                data[$(this).prop('name')] = $(this).val();
            });
        });
    });
  </script>
@endpush
@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
{{-- custom flatpickr --}}
<script src="{{asset('assets/js/custom/flatpickr.js')}}"></script>
<script src="{{asset('assets/js/custom/select2.js')}}"></script>
@endsection
