@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Users')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2/build/css/intlTelInput.css">
<style>
  .iti--show-flags {
    width: 100%;
  }
</style>
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/libs/intlTelInput/intlTelInput.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/admin-roles-permissions.js')}}></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script>
  function initIntlTel(){
    var input = document.querySelector("#phone");
    window.itiPhone = intlTelInput(input, {
      utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2/build/js/utils.js",
      initialCountry: "auto",
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
@includeWhen(isset($company) ,'admin.pages.company.header', ['tab' => 'users'])
  <div class="mt-3  col-12">
    <div class="card">
      <h5 class="card-header">Search Filter</h5>
      <form class="js-datatable-filter-form">
        <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 mx-3 gap-md-0">
          @isset($companies)
            <div class="col-md-4 user_role">
              <select name="filter_companies[]" class="form-select select2" multiple data-placeholder="Select Company">
                @forelse ($companies as $id => $company)
                  <option value="{{$id}}"> {{$company}} </option>
                @empty
                @endforelse

              </select>
            </div>
          @endisset
          <div class="col-md-4 user_plan">
            <select name="filter_status[]" class="form-select select2" multiple data-placeholder="User Status">
              @forelse ($statuses as $status)
                <option value="{{$status}}">{{ucfirst($status)}}</option>
              @empty
              @endforelse
              </select>
            </div>
          <div class="col-md-4 user_status">
            <select name="filer_roles[]" class="form-select select2" multiple data-placeholder="User Role">
              @forelse ($roles as $role)
                <option value="{{$role}}">{{$role}}</option>
              @empty
              @endforelse
            </select>
          </div>
        </div>
      </form>
      <div class="card-body">
        {{$dataTable->table()}}
      </div>
    </div>
  </div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      $(document).ready(function () {
          $('.js-datatable-filter-form :input').on('change', function (e) {
              window.LaravelDataTables["users_dataTable"].draw();
          });

          $('#users_dataTable').on('preXhr.dt', function ( e, settings, data ) {
              $('.js-datatable-filter-form :input').each(function () {
                  data[$(this).prop('name')] = $(this).val();
              });
          });
      });
    </script>
@endpush
