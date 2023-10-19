@extends('admin/layouts/layoutMaster')

@section('title', 'Medium Details')

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
  <span class="text-muted fw-light">Account Settings /</span> Medium
</h4>

<div class="row">
  <div class="col-md-12">
  {{-- @include('admin.pages.account._partials.tabs') --}}
    <div class="card mb-4">
      <h5 class="card-header">Medium Details</h5>
       {!! Form::model($medium, ['route' => ['admin.mediums.update', $medium->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
      <hr class="my-0">
      <div class="card-body">
          @csrf
          {{-- @method('PUT') --}}
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="Name" class="form-label">Medium Name</label>
              <input class="form-control" type="text" id="Name" name="name" value="{{ old('name') ?? $medium->name }}" autofocus />
              @error('first_name')<div class="text-danger">{{ $message }}</div>@enderror
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
