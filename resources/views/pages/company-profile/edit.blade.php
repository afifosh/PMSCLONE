@extends('layouts/layoutMaster')

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
<script src="{{asset('assets/js/custom/company-profile-page.js')}}"></script>
<script>
</script>
@endsection

@section('content')
<div class="col-12 mb-4">
  <div class="bs-stepper wizard-numbered mt-2">
    <div class="bs-stepper-header">
      <div class="step step-index-0" data-target="#company-details" data-href="{{route('company.editDetails')}}">
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
      <div class="step step-index-1" data-target="#company-contacts" data-href="{{route('company.contacts.index')}}">
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
      <div class="step step-index-2" data-target="#company-addresses" data-href="{{route('company.addresses.index')}}">
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
      <div class="step step-index-3" data-target="#company-documents" data-href="{{route('company.kyc-documents.index')}}">
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
      <div class="step step-index-4" data-target="#company-bank-accounts" data-href="{{route('company.bank-accounts.index')}}">
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
        <!-- Account Details -->
        <div id="company-details" class="content">
          @include('pages.company-profile.detail.index')
        </div>
        <!-- Personal Info -->
        <div id="company-contacts" class="content">
        </div>
        <!-- Social Links -->
        <div id="company-addresses" class="content">
        </div>
        <div id="company-documents" class="content">
        </div>
        <div id="company-bank-accounts" class="content">
        </div>
    </div>
  </div>
</div>
@endsection
