@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Artworks')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2/build/css/intlTelInput.css">
{{-- <link rel="stylesheet" href="{{asset('app-assets/css/bootstrap.min.css')}}">  --}}
<style>
  .iti--show-flags {
    width: 100%;
  }
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
{{-- <script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script> --}}
<script src="{{asset('assets/libs/intlTelInput/intlTelInput.js')}}"></script>
@endsection

@section('page-script')
{{-- <script src={{asset('assets/js/custom/admin-roles-permissions.js')}}></script> --}}
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  function initIntlTel(){
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
    if($(input).val() != ''){
      checkUtilInitialized();
    }
  }
  function checkUtilInitialized() {
    if (window.intlTelInputUtils) {
      validatePhone()
    } else {
      setTimeout(checkUtilInitialized, 50); // wait 50 ms
    }
  }
  $(document).on('keyup', '#phone', function(){
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
<h4 class="fw-semibold mb-4">{{__('Artworks')}}</h4>

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
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script>

          // Event handler for when an option is selected
$(document).on('select2:close', '#artwork-mediums-id', function(e) {
    var $me = $(this);
    var $tag = $me.find('option[data-select2-tag]');
    //We only want to select this tag if its the only tag there
    if ($tag && $tag.length && $me.find('option').length === 1) {
        $me.val($tag.attr('value'));
        $me.trigger('change');
        //Do stuff with $me.val()
    }
});
$(document).on('select2:select', '#artwork-mediums-id', function(e) {
  var data = e.params.data;
  if (data.isTag) { // Assuming `isNew` is a property you add to new tags
    // Handle new tag creation here
    // alert('New tag detected: ' + data.text);
    $.ajax({
            url: "{{ route('admin.mediums.store') }}",
            type: 'POST',
    data: {
        name: $(this).select2('data')[0].text.replace(/^\+ Add: /, ''),
    },
    success: function(response) {
        if (response.success) {
            // // Create a new option using the response data
            // var newOption = new Option(response.data.name, response.data.id, true, true);
            // // Append the new option to the Select2 element
            // $(this).append(newOption).trigger('change');
            toast_success(response.message);
        } else {
            // Handle the case where the server response indicates failure
            toast_danger(response.message);
            console.error('Failed to add new medium:', response.message);
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        // Handle AJAX errors
        toast_danger(e.statusText);
        console.error('AJAX error:', textStatus, errorThrown);
    }
});

  //   $('#artwork-mediums-id').val("").trigger("change");
  //   e.stopPropagation();
  //   return false;

  //   $('#artwork-mediums-id').val(null).trigger('change');
  //   return false;

  //   alert('New tag detected: ' + data.text);
  // e.preventDefault();
  // $(this).select2('close');


  } else {
    // Handle regular selection
    // alert('You have selected: ' + data.text);
  }
});

    </script>
@endpush
