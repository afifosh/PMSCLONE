@extends('admin/layouts/layoutMaster')

@section('title', 'artwork Profile Details')

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
      initialmedium: "auto",
      geoIpLookup: function(success) {
        fetch("https://api.medium.is")
          .then(function(response) {
            if (!response.ok) return success("");
            return response.json();
          })
          .then(function(ipdata) {
            success(ipdata.medium);
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
      $('#itiPhonemedium').val(itiPhone.getSelectedmediumData().iso2)
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
  <span class="text-muted fw-light">Account Settings /</span> artwork
</h4>

<div class="row">
  <div class="col-md-12">
  {{-- @include('admin.pages.account._partials.tabs') --}}
    <div class="card mb-4">
      <h5 class="card-header">artwork Profile Details</h5>
      {{-- {{    dd($artwork); }} --}}
      <!-- Account -->
      {{-- <form action="{{ route('admin.artworks.update', ['artist' => $artwork->id]) }}" method="POST">
      <form action="{{route('admin.artworks.update')}}" method="POST" enctype="multipart/form-data"> --}}
        {{-- <form method="POST" action="{{ route('admin.artworks.update', $artwork->id) }}">  --}}
       {!! Form::model($artwork, ['route' => ['admin.artworks.update', $artwork->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
      <div class="card-body">
        <div class="d-flex align-items-start align-items-sm-center gap-4">
          <img src="{{ $artwork->featured_image }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
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
            <div class="mb-3 col-md-12">
              <label for="title" class="form-label">Artwork Title</label>
              <input class="form-control" type="text" id="title" name="title" value="{{ old('title') ?? $artwork->title }}" autofocus />
              @error('title')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3 col-md-12">
              <label for="year" class="form-label">Year</label>
              <input class="form-control" type="text" id="year" name="year" value="{{ old('year') ?? $artwork->year }}" />
              @error('year')<div class="text-danger">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3 col-md-12">
            {{ Form::label('medium_id', __('Medium'), ['class' => 'form-label']) }}
            {!! Form::select('medium_id', $mediums, $artwork->medium_id, [
            'class' => 'form-select globalOfSelect2Remote',
            'data-url' => route('resource-select', ['Medium']),
            'id' => 'user-mediums-id',
            ]) !!}
          </div>          
          <div class="mb-3 col-md-12">
              <label for="dimension" class="form-label">Dimension</label>
              <input class="form-control" type="text" id="dimension" name="dimension" value="{{ old('dimension') ?? $artwork->dimension }}" />
              @error('dimension')<div class="text-danger">{{ $message }}</div>@enderror
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
