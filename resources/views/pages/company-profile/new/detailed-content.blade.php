@extends('layouts/layoutMaster')

@section('title', 'User Profile - Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
<script src="{{asset('assets/js/custom/company-profile-page.js')}}"></script>
<script src="{{asset('assets/js/custom/toastr-helpers.js')}}"></script>
@endsection

@section('content')
  @include('pages.company-profile.new.detailed-content-header', ['tab' => 'details'])
<div class="row">
  <div class="col-12">
    <div id="details-card" class="card card-action mb-4" data-href="{{ route('company.editDetails')}}">
      @include('pages.company-profile.new.detailed-content.card-actions', ['title' => 'Company Details', 'desc' => 'Manage Company Details'])
      <div class="collapse show">
        @include('pages.company-profile.new.detailed-content.details')
      </div>
    </div>
    <div id="contact-persons-card" class="card card-action mb-4" data-href="{{ route('company.contacts.index')}}">
      @include('pages.company-profile.new.detailed-content.card-actions', ['title' => 'Contact Persons', 'desc' => 'Manage Contact Persons'])
      <div class="collapse show">
        @include('pages.company-profile.new.detailed-content.contacts')
      </div>
    </div>
    <div id="addresses-card" class="card card-action mb-4" data-href="{{route('company.addresses.index')}}">
      @include('pages.company-profile.new.detailed-content.card-actions', ['title' => 'Company Addresses', 'desc' => 'Manage Company Addresses'])
      <div class="collapse show">
        @include('pages.company-profile.new.detailed-content.addresses')
      </div>
    </div>
    <div id="documents-card" class="card card-action mb-4" data-href="{{ route('company.kyc-documents.index')}}">
      @include('pages.company-profile.new.detailed-content.card-actions', ['title' => 'Verification Documents', 'desc' => 'Manage Verification Documents'])
      <div class="collapse show">
        @include('pages.company-profile.new.detailed-content.documents')
      </div>
    </div>
    <div id="accounts-card" class="card card-action mb-4" data-href="{{ route('company.bank-accounts.index')}}">
      @include('pages.company-profile.new.detailed-content.card-actions', ['title' => 'Bank Accounts', 'desc' => 'Manage Bank Accounts'])
      <div class="collapse show">
        @include('pages.company-profile.new.detailed-content.accounts')
      </div>
    </div>
    <div class="card card-action mb-4">
      <div class="card-body d-flex justify-content-end">
        <a href="{{route('company.submitApprovalRequest')}}" class="btn btn-outline-light waves-effect bg-dark {{auth()->user()->company->canBeSentForApproval() ? '': 'disabled'}}" type="button"> <span class="align-middle d-sm-inline-block me-sm-1">Send for Approval</span></a>
      </div>
    </div>
</div>
@endsection
