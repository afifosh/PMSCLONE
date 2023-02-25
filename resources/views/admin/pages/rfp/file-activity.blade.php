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

