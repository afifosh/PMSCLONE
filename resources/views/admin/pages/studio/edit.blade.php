@extends('admin/layouts/layoutMaster')

@section('title', 'Studio Profile Details')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2/build/css/intlTelInput.css">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/libs/intlTelInput/intlTelInput.js')}}"></script>
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
    initModalSelect2();
    var input = document.querySelector("#phone");
    window.itiPhone = intlTelInput(input, {
      utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2/build/js/utils.js",
      initialCountry: "auto",
      geoIpLookup: function(success) {
        fetch("https://api.country.is")
          .then(function(response) {
            if (!response.ok) return success("");
            return response.json();
          })
          .then(function(ipdata) {
            success(ipdata.country);
          });
        },
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
  <span class="text-muted fw-light">Account Settings /</span> Studio
</h4>

<div class="row">
  <div class="col-md-12">
  {{-- @include('admin.pages.account._partials.tabs') --}}
    <div class="card mb-4">
      <h5 class="card-header">Studio Profile Details</h5>
       {!! Form::model($studio, ['route' => ['admin.studios.update', $studio->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
      <div class="card-body">
        <div class="d-flex align-items-start align-items-sm-center gap-4">
          <img src="{{ $studio->avatar }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
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
          {{-- @method('PUT') --}}
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="name" class="form-label">Name</label>
              <input class="form-control" type="text" id="name" name="name" value="{{ old('name') ?? $studio->name }}" autofocus />
              @error('name')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 col-md-6">
              <label for="gender" class="form-label">Gender</label>
              <select id="gender" name="gender" class="select2 form-select">
                  <option value="Male" {{ old('gender') == 'Male' || $studio->gender == 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ old('gender') == 'Female' || $studio->gender == 'Female' ? 'selected' : '' }}>Female</option>
                  <option value="Other" {{ old('gender') == 'Other' || $studio->gender == 'Other' ? 'selected' : '' }}>Other</option>
              </select>
              @error('gender')<div class="text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 col-md-6">
                <label for="email" class="form-label">Email</label>
                <input class="form-control" type="email" id="email" name="email"
                    value="{{ old('email') ?? $studio->email }}" />
                @error('email')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 col-md-6">
                <label for="website" class="form-label">Website</label>
                <input class="form-control" type="text" id="website" name="website"
                    value="{{ old('website') ?? $studio->website }}" />
                @error('website')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 col-md-6">
                <label for="jobTitle" class="form-label">Job Title</label>
                <input class="form-control" type="text" id="jobTitle" name="job_title"
                    value="{{ old('job_title') ?? $studio->job_title }}" />
                @error('job_title')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 col-md-6">
                <label for="birthDate" class="form-label">Birth Date</label>
                <input class="form-control" type="date" id="birthDate" name="birth_date"
                    value="{{ old('birth_date') ?? $studio->birth_date }}" />
                @error('birth_date')<div class="text-danger">{{ $message }}</div>@enderror
            </div>


            <div class="mb-3 col-md-6">
              <label class="form-label" for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{old('phone') ?? $studio->phone}}"/>
                {{-- @error('phone')<div class="text-danger">{{ $message }}</div>@enderror --}}
              <span id="itiPhone"></span>
              <input type="hidden" id="itiPhoneCountry" name="phone_country">
            </div>
            <div class="mb-3 col-md-6">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" id="address" name="address" value="{{ old('address') ?? $studio->address }}" placeholder="Address" />
              @error('address')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 col-md-6">
              <label for="zipCode" class="form-label">Zip Code</label>
              <input type="text" class="form-control" id="zipCode" name="zip_code" value="{{ old('zip_code') ?? $studio->zip_code }}" placeholder="231465" maxlength="6" />
              @error('zip_code')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 col-6">
              {{ Form::label('country_id', __('Country'), ['class' => 'form-label']) }}
              {!! Form::select('country_id', $countries, $studio->country_id, [
              'class' => 'form-select globalOfSelect2Remote',
              'data-url' => route('resource-select', ['Country']),
              'id' => 'user-countries-id',
              ]) !!}
            </div>
            {{-- states --}}
            <div class="mb-3 col-6">
              {{ Form::label('state_id', __('State'), ['class' => 'form-label']) }}
              {!! Form::select('state_id', $states, $studio->state_id, [
              'class' => 'form-select globalOfSelect2Remote',
              'data-url' => route('resource-select', ['State']),
              'data-dependent_id' => 'user-countries-id',
              'id' => 'user-state-id',
              ]) !!}
            </div>
            {{-- cities --}}
            <div class="mb-3 col-6">
              {{ Form::label('city_id', __('City'), ['class' => 'form-label']) }}
              {!! Form::select('city_id', $cities, $studio->city_id, [
              'class' => 'form-select globalOfSelect2Remote',
              'data-url' => route('resource-select', ['City']),
              'data-dependent_id' => 'user-state-id'
              ]) !!}
            </div>
            <div class="mb-3 col-md-6">
              <label for="language" class="form-label">Language</label>
              <select id="language" name="language" class="select2 form-select">
                <option value="">Select Language</option>
                @forelse ($languages as $key => $lang)
                    <option value="{{ $key }}" {{old('language') == $key || $key == $studio->language ? 'selected' : '' }}>{{ $lang }}</option>
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
                    <option value="{{ $tz['value'] }}" {{ $tz['value'] == old('timezone') || $tz['value'] == $studio->timezone ? 'selected' : ''}}>{{ $tz['label'] }}</option>
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
                    <option value="{{ $key }}" {{old('currency') == $key || $key == $studio->currency ? 'selected' : '' }}>{{ $cur }}</option>
                @empty
                @endforelse
              </select>
              @error('currency')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="mt-2">
            <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save changes') }}</button>
            <button type="reset" class="btn btn-label-secondary">Cancel</button>
          </div>
          {!! Form::close() !!}
      </div>
      <!-- /Account -->
    </div>


  </div>
</div>

@endsection
