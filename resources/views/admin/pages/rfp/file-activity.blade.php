@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'Files Activity')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/ajax.js')}}></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  $(document).ready(function () {
    initFlatPickr();
  });
</script>
@endsection

@section('content')
@include('admin.pages.rfp.header', ['tab' => 'files-activity'])

<div class="card">
  <h5 class="card-header">Search Filter</h5>
  <form id="asdasdfdsf" class="js-datatable-filter-form" method="GET" action="{{route('admin.draft-rfps.files_activity', $draft_rfp)}}">
    <div class="d-flex align-items-center row pb-2 gap-3 mx-3 gap-md-0">
      <div class="col-md-4">
        {{ Form::select('filter_files[]', $files, request('filter_files'), ['class' => 'form-select select2', 'data-placeholder' => 'Files', 'multiple']) }}
      </div>
      <div class="col-md-4">
        @php
          $optionParameters = collect($users)->mapWithKeys(function ($item) {
              return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
          })->all();
        @endphp
        {{ Form::select('filter_actioner[]', $users->pluck('email', 'id'), request('filter_actioner'), ['class' => 'form-select select2User', 'data-placeholder' => 'Users', 'multiple'], $optionParameters) }}
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <input name="filter_date_range" value="{{request('filter_date_range')}}" type="text" class="form-control flatpickr" data-flatpickr='{"mode": "range", "dateFormat": "Y-m-d"}' placeholder="Date Range">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="mt-3">
        <div class="btn-flt float-end mx-4">
            <button type="reset" class="btn btn-secondary clear-form">{{ __('Clear') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
        </div>
      </div>
    </div>
  </form>
  <div class="card-body pb-0">
    <ul class="timeline ms-1 mb-0">
      @forelse ($logs as $log)
        <li class="timeline-item timeline-item-transparent">
          <span class="timeline-point timeline-point-primary"></span>
          <div class="timeline-event">
            <div class="timeline-header">
              <h6 class="mb-0">{{ $log->log }}</h6>
              <small class="text-muted">{{$log->created_at->diffForHumans()}}</small>
            </div>
            <p class="mb-2">{{ $log->actioner->full_name }} {{ $log->log}} @ {{formatDateTime($log->created_at)}}</p>
            <div class="d-flex flex-wrap">
              <div class="avatar me-2">
                <img src="{{ $log->actioner->avatar }}" alt="Avatar" class="rounded-circle" />
              </div>
              <div class="ms-1">
                <h6 class="mb-0">{{ $log->actioner->full_name }}</h6>
                <span>{{ $log->actioner->email }}</span>
              </div>
            </div>
          </div>
        </li>
      @empty
        <li class="timeline-item timeline-item-transparent">
          <span class="timeline-point timeline-point-primary"></span>
          <div class="timeline-event">
            <div class="timeline-header">
              <h6 class="mb-0">No Activity</h6>
            </div>
          </div>
        </li>
      @endforelse
    </ul>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card-footer d-flex justify-content-end">
        {{$logs->links()}}
      </div>
    </div>
  </div>
  {{-- {{$logs->links()}} --}}
</div>



@endsection

