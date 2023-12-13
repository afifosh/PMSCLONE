@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Programs')

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
<script src={{asset('assets/js/custom/admin-roles-permissions.js')}}></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
@endsection

@section('content')

<div class="card mb-4">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
            <div>
              <h3 class="mb-1">{{ $totalPrograms }}</h3>
              <p class="mb-0">Programs</p>
            </div>
            <span class="avatar me-sm-4">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti ti-user ti-md"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-4">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
            <div>
              <h3 class="mb-1">{{ $totalContracts }}</h3>
              <p class="mb-0">Contracts</p>
            </div>
            <span class="avatar me-lg-4">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti ti-file-invoice ti-md"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
            <div>
              <h3 class="mb-1">{{ $totalLinkedContracts }}</h3>
              <p class="mb-0">Linked Contracts</p>
            </div>
            <span class="avatar me-sm-4">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti ti-checks ti-md"></i></span>
            </span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h3 class="mb-1">{{ $totalUnlinkedContracts }}</h3>
              <p class="mb-0">Not Linked Contracts</p>
            </div>
            <span class="avatar">
              <span class="avatar-initial bg-label-secondary rounded"><i class="ti ti-circle-off ti-md"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

  <div class="mt-3  col-12">
    <div class="card">
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
