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
@isset($draft_rfp)
@include('admin.pages.rfp.header', ['tab' => 'files-activity'])
@endisset
@if (!@$draft_rfp)
  <div class="row breadcrumbs-top">
    <div class="col-12 d-flex justify-content-between">
      <div class="d-flex">
        <span class="content-header-title float-left border-end px-2 me-2 h4">File Activity</span>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb pt-1">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}">Home</a>
                </li>
                @if(@$rfp)
                  <li class="breadcrumb-item">
                      <a href="{{ route('admin.draft-rfps.index') }}">Draft RFPs</a>
                  </li>
                  <li class="breadcrumb-item">
                      <a href="{{ route('admin.draft-rfps.show', $file->rfp_id) }}">{{ $file->rfp->name }}</a>
                  </li>
                  <li class="breadcrumb-item">
                      <a href="{{ route('admin.draft-rfps.files.index', ['draft_rfp' => $file->rfp_id]) }}">Files</a>
                  </li>
                @else
                  <li class="breadcrumb-item">
                    <a href="{{ route('admin.draft-rfps.index') }}">Shared Files</a>
                  </li>
                @endif
                <li class="breadcrumb-item active">{{ $file->title }}</li>
            </ol>
        </nav>
      </div>
      <div>
        <div class="btn-group">
          <button class="btn btn-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.shared-files.file-versions', ['file' => $file->id, 'rfp' => $rfp])}}">Versions History</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
@endif

<div class="card">
  <h5 class="card-header">Search Filter</h5>
  @isset($draft_rfp)
    <form method="GET" action="{{route('admin.draft-rfps.files_activity', $draft_rfp)}}">
  @else
    <form method="GET" action="{{ route('admin.shared-files.file-activity', ['file' => $file->id]) }}">
  @endisset

    <div class="d-flex align-items-center row pb-2 gap-3 mx-3 gap-md-0">
      @isset($files)
        <div class="col-md-4">
          {{ Form::select('filter_files[]', $files, request('filter_files'), ['class' => 'form-select select2', 'data-placeholder' => 'Files', 'multiple']) }}
        </div>
      @endisset
      @isset($users)
        <div class="col-md-4">
          @php
            $optionParameters = collect($users)->mapWithKeys(function ($item) {
                return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
            })->all();
          @endphp
          {{ Form::select('filter_actioner[]', $users->pluck('email', 'id'), request('filter_actioner'), ['class' => 'form-select select2User', 'data-placeholder' => 'Users', 'multiple'], $optionParameters) }}
        </div>
      @endisset
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
  @include('admin.pages.rfp.file-activity-logs', ['logs' => $logs])
  <div class="row">
    <div class="col-12">
      <div class="card-footer d-flex justify-content-end">
        {{$logs->links()}}
      </div>
    </div>
  </div>
</div>
@endsection

