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
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')
@include('admin.pages.rfp.header', ['tab' => 'files-activity'])

<div class="card">
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

