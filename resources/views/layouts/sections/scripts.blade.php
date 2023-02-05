<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/node-waves/node-waves.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/hammer/hammer.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/i18n/i18n.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/typeahead-js/typeahead.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/toastr/toastr.js')) }}"></script>
<script src="{{ asset(mix('assets/js/custom/ajax.js')) }}"></script>
<script src="{{ asset(mix('assets/js/custom/toastr-helpers.js')) }}"></script>
@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>
<script>
  $(document).ready(function () {
    @if(session()->has('success'))
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title:"{{session('success')}}",
            showConfirmButton: false,
            timer: 1500,
            customClass: {
              confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          });
    @endif
    @if(session()->has('error'))
        Swal.fire({
            position: 'top-end',
            icon: 'error',
            title:"{{session('error')}}",
            showConfirmButton: false,
            timer: 1500,
            customClass: {
              confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          });
    @endif
    @if(session()->has('status'))
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title:"{{ucwords(str_replace('-', ' ', session('status')))}}",
            showConfirmButton: false,
            timer: 1500,
            customClass: {
              confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          });
    @endif
    console.log('{!!json_encode(session()->all())!!}');
  });
 </script>

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
