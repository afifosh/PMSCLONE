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
  $(function () {
    let accountUserImage = document.getElementById('uploadedAvatar');
    const fileInput = document.querySelector('.account-file-input'),
      resetFileInput = document.querySelector('.account-image-reset');

    if (accountUserImage) {
      const resetImage = accountUserImage.src;
      fileInput.onchange = () => {
        if (fileInput.files[0]) {
          accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
        }
      };
      resetFileInput.onclick = () => {
        fileInput.value = '';
        accountUserImage.src = resetImage;
      };
    }
  });

  $('[data-switch-toggle]').on('click', function () {
    var target = $(this).data('switch-toggle');
    $(this).is(":checked") ? $(target).removeClass('d-none') : $(target).addClass('d-none');
  });
  $('.save-draft').on('click', function () {
    $(this).closest('form').find('input[name="submit_type"]').val('draft');
    $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
  });
  $('.btn-next').on('click', function () {
    $(this).closest('form').find('input[name="submit_type"]').val('submit');
    $(this).closest('form').find('[data-form="ajax-form"]').trigger('click');
  });
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
      {{-- <form onSubmit="return false"> --}}
        <!-- Account Details -->
        <div id="company-details" class="content">
          <div class="content-header mb-3">
            <h6 class="mb-0">Company Details</h6>
            <small>Enter Company Details.</small>
          </div>
          @include('pages.company-profile.details-form')
        </div>
        <!-- Personal Info -->
        <div id="company-contacts" class="content">
          <div class="content-header mb-3">
            <h6 class="mb-0">Contacts</h6>
            <small>Add Your Contact Persons</small>
          </div>
          <div class="row g-3 form-repeater">
            <div data-repeater-list="group-a">
              <div class="p-3 mt-4 border rounded position-relative" data-repeater-item style="background-color: #f1f0f2;">
                  <button class="btn btn-xs rounded-circle  btn-label-danger position-absolute top-0 start-100 translate-middle" data-repeater-delete>
                    <i class="ti ti-x ti-xs"></i>
                  </button>
                <div class="row">
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-1">Contact Type</label>
                    <select id="form-repeater-1-1" class="form-select">
                      <option value="1">Owner</option>
                      <option value="2">Employee</option>
                    </select>
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-2">Title</label>
                    <input type="text" id="form-repeater-1-2" class="form-control" placeholder="Title" />
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-3">First Name</label>
                    <input type="text" id="form-repeater-1-3" class="form-control" placeholder="First Name" />
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-4">Last Name</label>
                    <input type="text" id="form-repeater-1-4" class="form-control" placeholder="Last Name" />
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-5">Postion</label>
                    <input type="text" id="form-repeater-1-5" class="form-control" placeholder="Postion" />
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-6">Phone</label>
                    <input type="text" id="form-repeater-1-6" class="form-control" placeholder="Phone" />
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-7">Mobile</label>
                    <input type="text" id="form-repeater-1-7" class="form-control" placeholder="Mobile" />
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-8">Fax</label>
                    <input type="text" id="form-repeater-1-8" class="form-control" placeholder="Fax" />
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-6 col-12 mb-0">
                    <label class="form-label" for="form-repeater-1-9">Email</label>
                    <input type="email" id="form-repeater-1-9" class="form-control" placeholder="Email" />
                  </div>
                  <div class="mb-3 col-lg-6 col-xl-6 col-12 mb-0">
                    <label for="form-repeater-1-10">POA Letter</label>
                    <input id="form-repeater-1-10" class="form-control" type="file" name="poa">
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-0 text-end">
              <button class="btn btn-primary" data-repeater-create>
                <i class="ti ti-plus me-1"></i>
                <span class="align-middle">Add</span>
              </button>
            </div>
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
        </div>
        <!-- Social Links -->
        <div id="company-addresses" class="content">
          <div class="content-header mb-3">
            <h6 class="mb-0">Addresses</h6>
            <small>Manage Addresses</small>
          </div>
          @include('pages.company-profile.addresses-form')
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
          @include('pages.company-profile.banks-form')
        </div>
    </div>
  </div>
</div>
@endsection
