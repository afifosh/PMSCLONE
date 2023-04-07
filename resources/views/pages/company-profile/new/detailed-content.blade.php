@extends('layouts/layoutMaster')

@section('title', 'User Profile - Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
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
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
<script src="{{asset('assets/js/custom/company-profile-page.js')}}"></script>
@endsection

@section('content')
  {{-- <div class="alert alert-danger position-fixed w-100" style="z-index:1" role="alert">
    Details Are Under Review, You cannot modifiy them.
  </div> --}}
<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ asset('assets/img/avatars/14.png') }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>John Doe</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item">
                  <i class='ti ti-color-swatch'></i> UX Designer
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-map-pin'></i> Vatican City
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-calendar'></i> Joined April 2021</li>
              </ul>
            </div>
            <div>
              <div class="d-flex justify-content-between">
                <span class="">Setup Progress</span>
                <span>{{auth()->user()->company->step_completed_count}}/5</span>
              </div>
              <div class="progress" style="height:10px; width:300px">
                <div class="progress-bar" role="progressbar" style="width: {{(auth()->user()->company->step_completed_count/5)*100}}%" aria-valuenow="{{(auth()->user()->company->step_completed_count/5)*100}}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<!-- Navbar pills -->
<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-sm-row mb-4">
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class='ti-xs ti ti-user-check me-1'></i> Details</a></li>
      <li class="nav-item"><a class="nav-link" href="#contact-persons-card"><i class='ti-xs ti ti-users me-1'></i> Contact Persons</a></li>
      <li class="nav-item"><a class="nav-link" href="#addresses-card"><i class='ti-xs ti ti-layout-grid me-1'></i> Addresses</a></li>
      <li class="nav-item"><a class="nav-link" href="#documents-card"><i class='ti-xs ti ti-link me-1'></i> Documents</a></li>
      <li class="nav-item"><a class="nav-link" href="#accounts-card"><i class='ti-xs ti ti-link me-1'></i> Bank Accounts</a></li>
    </ul>
  </div>
</div>
<!--/ Navbar pills -->
<div class="card">
  <div id="sticky-wrapper" class="sticky-wrapper is-sticky" style="height: 86.0833px;">
      <div style="width: 1392px; position: fixed; top: 69px; z-index: 9;"
          class="card-header sticky-element bg-label-warning ">
          <div style="" role="alert" class="alert alert-danger ">
              Details Are Under Review, You cannot modifiy them.
              <div></div>
          </div>
          <div
              class="card-header sticky-element bg-label-warning d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row p-0">
              <h5 class="card-title mb-sm-0 me-2">
                  Details Are Under Review, You cannot modifiy them.
              </h5>
              <div class="action-btns">
                  <button class="btn btn-label-primary me-3 waves-effect">
                      <span class="align-middle"> Back</span>
                  </button>
                  <a href="{{route('company.submitApprovalRequest')}}" class="btn btn-primary waves-effect waves-light {{auth()->user()->company->canBeSentForApproval() ? '': 'disabled'}}">Send for Approval</a>
              </div>
          </div>

      </div>
  </div>
</div>
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
