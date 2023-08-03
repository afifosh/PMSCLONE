@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Projects')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
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
  <div class="mt-3  col-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <h4>Projects Summary</h4>
          <div class="row">
            <div class="col-2 d-flex border-end">
              <h5 class="mx-3">{{$summary->where('status', 0)->first()->project_count ?? 0}}</h5>
              <span class="">Not Started</span>
            </div>
            <div class="col-2 d-flex border-end">
              <h5 class="mx-3">{{$summary->where('status', 1)->first()->project_count ?? 0}}</h5>
              <span class="text-primary">In Progress</span>
            </div>
            <div class="col-2 d-flex border-end">
              <h5 class="mx-3">{{$summary->where('status', 2)->first()->project_count ?? 0}}</h5>
              <span class="text-warning">On Hold</span>
            </div>
            <div class="col-2 d-flex border-end">
              <h5 class="mx-3">{{$summary->where('status', 3)->first()->project_count ?? 0}}</h5>
              <span class="text-muted">Cancelled</span>
            </div>
            <div class="col-2 d-flex">
              <h5 class="mx-3">{{$summary->where('status', 4)->first()->project_count ?? 0}}</h5>
              <span class="text-success">Finished</span>
            </div>
        </div>
        <hr class="mt-2">
        {{$dataTable->table()}}
      </div>
    </div>
  </div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
@endpush
