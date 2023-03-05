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
<script src="{{ asset(mix('assets/js/custom/bell-notifications.js')) }}"></script>
@auth
@if(Auth::getDefaultDriver() === 'admin' && Route::currentRouteName() !== 'admin.auth.lock')
<script src="{{ asset(mix('assets/js/bootstrap-session-timeout.js')) }}"></script>
<script>
  var keepAliveUrl = "{{ route('admin.alive') }}"
  var logoutUrl = "{{ route('admin.logout') }}"
  var redirUrl = "{{ route('admin.logout') }}"
  var warnAfter = +"{{ config('auth.timeout_warning_seconds') }}"
  var redirAfter = +"{{ config('auth.timeout_after_seconds') }}" + warnAfter
</script>
<script src="{{ asset(mix('assets/js/custom/session-timeout.js')) }}"></script>
@endif
@endauth

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
<script>
  $(document).ready(function () {
    @if(session()->has('success'))
    toast_success("{{session('success')}}")
    @endif
    @if(session()->has('error'))
    toast_danger("{{session('error')}}");
    @endif
    @if(session()->has('status'))
    toast_success("{{ucwords(str_replace('-', ' ', session('status')))}}");
    @endif
  });
 </script>
