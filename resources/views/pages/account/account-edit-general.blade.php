@extends('layouts/layoutMaster')

@section('title', 'Account settings - Account')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.css">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-account.js')}}"></script>
<script>
  document.addEventListener('DOMContentLoaded', checkUtilInitialized);
    function checkUtilInitialized() {
      if (window.intlTelInputUtils) {
        validatePhone()
      } else {
        setTimeout(checkUtilInitialized, 50); // wait 50 ms
      }
    }
  $(function () {
    var input = document.querySelector("#phone");
    window.itiPhone = intlTelInput(input, {
      utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
      initialCountry: "auto",
    });
  });
  $('#phone').keyup(function (e) {
    validatePhone()
  });
  function validatePhone(){
    var isValid = itiPhone.isValidNumber();
    $('#phone').val(itiPhone.getNumber());
    if(isValid){
      $('#itiPhone').text('')
      $('#itiPhoneCountry').val(itiPhone.getSelectedCountryData().iso2)
    }else{
      $('#itiPhone').text('Invalid phone number')
      $('#itiPhone').css('color', 'red')
    }
  }
</script>
@endsection

@section('content')

<style>
  .iti { width: 100%; }
</style>
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Account
</h4>

<div class="row">
  <div class="col-md-12">
    @include('pages.account._partials.tabs')
    <div class="card mb-4">
      <h5 class="card-header">Profile Details</h5>
      <!-- Account -->
      <div class="card-body">
      <form method="POST" action="{{ route('user.user-account.update', auth()->id()) }}"  enctype="multipart/form-data">
        <div class="card-body">
          <div class="d-flex align-items-start align-items-sm-center gap-4">
            <img src="{{ auth()->user()->avatar }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
            <div class="button-wrapper">
                @csrf
                <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                  <span class="d-none d-sm-block">Upload new photo</span>
                  <i class="ti ti-upload d-block d-sm-none"></i>
                  <input type="file" id="upload" name="profile" class="account-file-input" hidden accept="image/png, image/jpeg" />
                </label>
                <button type="button" class="btn btn-label-secondary account-image-reset mb-3">
                  <i class="ti ti-refresh-dot d-block d-sm-none "></i>
                  <span class="d-none d-sm-block">Reset</span>
                </button>
                <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 800K</div>
                @error('profile')
                  <div class="alert alert-danger alert-dismissible my-2">
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      {{ $message }}
                  </div>
                @enderror
            </div>
          </div>
        </div>
        <hr class="my-0">
        <div class="card-body">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="firstName" class="form-label">First Name</label>
                <input class="form-control" type="text" id="firstName" name="first_name" value="{{ old('first_name') ?? auth()->user()->first_name }}" autofocus />
                @error('first_name')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label for="lastName" class="form-label">Last Name</label>
                <input class="form-control" type="text" name="last_name" id="lastName" value="{{ old('last_name') ?? auth()->user()->last_name }}" />
                @error('last_name')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label for="email" class="form-label">E-mail</label>
                <input class="form-control" type="text" id="email" name="email" value="{{old('email') ?? auth()->user()->email}}" placeholder="Email" />
                @error('email')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label class="form-label" for="phone">Phone Number</label>
                  <input type="text" id="phone" name="phone" class="form-control" value="{{old('phone') ?? auth()->user()->phone}}"/>
                  @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
                <span id="itiPhone"></span>
                <input type="hidden" id="itiPhoneCountry" name="phone_country">
              </div>
              <div class="mb-3 col-md-6">
                <label for="email" class="form-label">Organization</label>
                <input class="form-control" type="text" value="{{old('email') ?? auth()->user()->company->name}}" disabled />
              </div>
              <div class="mb-3 col-md-6">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') ?? auth()->user()->address }}" placeholder="Address" />
                @error('address')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label for="state" class="form-label">State</label>
                <input class="form-control" type="text" id="state" name="state" value="{{ old('state') ?? auth()->user()->state }}" placeholder="California" />
                @error('state')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label for="zipCode" class="form-label">Zip Code</label>
                <input type="text" class="form-control" id="zipCode" name="zip_code" value="{{ old('zip_code') ?? auth()->user()->zip_code }}" placeholder="231465" maxlength="6" />
                @error('zip_code')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label class="form-label" for="country">Country</label>
                <select id="country" name="country_id" class="select2 form-select">
                  <option value="">Select</option>
                  @forelse ($countries as $country)
                      <option value="{{ $country->id }}" {{old('country_id') == $country->id || $country->id == auth()->user()->country_id ? 'selected' : '' }}>{{ $country->name }}</option>
                  @empty
                  @endforelse
                </select>
                @error('country_id')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label for="language" class="form-label">Language</label>
                <select id="language" name="language" class="select2 form-select">
                  <option value="">Select Language</option>
                  @forelse ($languages as $key => $lang)
                      <option value="{{ $key }}" {{old('language') == $key || $key == auth()->user()->language ? 'selected' : '' }}>{{ $lang }}</option>
                  @empty
                  @endforelse
                </select>
                @error('language')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label for="timeZones" class="form-label">Timezone</label>
                <select id="timeZones" name="timezone" class="select2 form-select">
                  <option value="">Select Timezone</option>
                  @forelse ($timezones as $tz)
                      <option value="{{ $tz['value'] }}" {{ $tz['value'] == old('timezone') || $tz['value'] == auth()->user()->timezone ? 'selected' : ''}}>{{ $tz['label'] }}</option>
                  @empty
                  @endforelse
                </select>
                @error('timezone')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3 col-md-6">
                <label for="currency" class="form-label">Currency</label>
                <select id="currency" name="currency" class="select2 form-select">
                  <option value="">Select Currency</option>
                  @forelse ($currencies as $key => $cur)
                      <option value="{{ $key }}" {{old('currency') == $key || $key == auth()->user()->currency ? 'selected' : '' }}>{{ $cur }}</option>
                  @empty
                  @endforelse
                </select>
                @error('currency')<div class="text-danger">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary me-2">Save changes</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          </div>
        </form>
      </div>
      <!-- /Account -->
    </div>
    <div class="card">
      <h5 class="card-header">Delete Account</h5>
      <div class="card-body">
        <div class="mb-3 col-12 mb-0">
          <div class="alert alert-warning">
            <h5 class="alert-heading mb-1">Are you sure you want to delete your account?</h5>
            <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
          </div>
        </div>
        <form id="formAccountDeactivation" onsubmit="return false">
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" />
            <label class="form-check-label" for="accountActivation">I confirm my account deactivation</label>
          </div>
          <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
