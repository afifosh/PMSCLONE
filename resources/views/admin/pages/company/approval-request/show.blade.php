@extends('admin.layouts.layoutMaster')

@section('title', 'Company Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/custom/admin-company-profile-page.js')}}"></script>
<script>
</script>
@endsection

@section('content')
<div class="col-12 mb-4">
  <div class="bs-stepper wizard-numbered mt-2">
    <div class="bs-stepper-header">
      <div class="step" data-target="#company-details">
        <button type="button" class="step-trigger">
          <span class="bs-stepper-circle">1</span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title">Company Details</span>
            <span class="bs-stepper-subtitle">Manage Details</span>
          </span>
        </button>
      </div>
      <div class="line">
        <i class="ti ti-chevron-right"></i>
      </div>
      <div class="step" data-target="#company-contacts">
        <button type="button" class="step-trigger">
          <span class="bs-stepper-circle">2</span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title">Contacts</span>
            <span class="bs-stepper-subtitle">Manage Contact Persons</span>
          </span>

        </button>
      </div>
      <div class="line">
        <i class="ti ti-chevron-right"></i>
      </div>
      <div class="step" data-target="#company-addresses">
        <button type="button" class="step-trigger">
          <span class="bs-stepper-circle">3</span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title">Address</span>
            <span class="bs-stepper-subtitle">Manage Addresses</span>
          </span>
        </button>
      </div>
      <div class="line">
        <i class="ti ti-chevron-right"></i>
      </div>
      <div class="step" data-target="#company-documents">
        <button type="button" class="step-trigger">
          <span class="bs-stepper-circle">4</span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title">Documents</span>
            <span class="bs-stepper-subtitle">Manage Legal Documents</span>
          </span>
        </button>
      </div>
      <div class="line">
        <i class="ti ti-chevron-right"></i>
      </div>
      <div class="step" data-target="#company-bank-accounts">
        <button type="button" class="step-trigger">
          <span class="bs-stepper-circle">5</span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title">Bank Accounts</span>
            <span class="bs-stepper-subtitle">Manage Bank Accounts</span>
          </span>
        </button>
      </div>
    </div>
    <div class="bs-stepper-content">
      {{-- < onSubmit="return false"> --}}
        <!-- Account Details -->
        <div id="company-details" class="content">
          <div class="content-header mb-3">
            <h6 class="mb-0">Company Details</h6>
            <small>Enter Company Details.</small>
          </div>
          @include('admin.pages.company.approval-request.details-form')
        </div>
        <!-- Personal Info -->
        <div id="company-contacts" class="content">
          @include('admin.pages.company.approval-request.contacts-form')
        </div>
        <!-- Social Links -->
        <div id="company-addresses" class="content">
          <div class="content-header mb-3">
            <h6 class="mb-0">Addresses</h6>
            <small>Manage Addresses</small>
          </div>
          @include('admin.pages.company.approval-request.addresses-form')
        </div>
        <div id="company-documents" class="content">
          <div class="col-12 d-flex justify-content-between">
            <button class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
              <span class="align-middle d-sm-inline-block d-none">Previous</span>
            </button>
            <div>
              <button class="btn btn-outline-secondary" type="button">Save Draft</button>
              <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
            </div>
          </div>
        </div>
        <div id="company-bank-accounts" class="content">
          @include('admin.pages.company.approval-request.banks-form')
        </div>
    </div>
  </div>
</div>
@endsection
