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
@auth
  @if(Route::currentRouteName() !== 'auth.lock' && Route::currentRouteName() !== 'admin.auth.lock')
  <!-- Session Timeout -->
  <script src="{{asset('assets/vendor/libs/timeout/bootstrap-session-timeout.js')}}"></script>
  @endif
@endauth
@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>
<script>
  $(document).ready(function() {
    @if(session()->has('success'))
    toast_success("{{session('success')}}")
    @endif
    @if(session()->has('error'))
    toast_danger("{{session('error')}}");
    @endif
    @if(session()->has('status'))
    toast_success("{{ucwords(str_replace('-', ' ', session('status')))}}");
    @endif

    let guard = "{{ config('fortify.guard') }}"
    @auth
      @if(Route::currentRouteName() !== 'auth.lock' && Route::currentRouteName() !== 'admin.auth.lock')
        $.sessionTimeout({
          keepAliveUrl: '/keep-alive',
          logoutUrl: guard === 'web' ? '/logout' : '/admin/logout',
          redirUrl: guard === 'web' ? '/auth/lock' : '/admin/auth/locked',
          warnAfter: +"{{ cache('timeout_warning_seconds') }}",
          redirAfter: +"{{ cache('timeout_after_seconds') }}",
          countdownBar: true,
          countdownMessage: 'Redirecting in {timer} seconds.',
          useLocalStorageSynchronization: true,
          clearWarningOnUserActivity: false,
        });
      @endif

      var site_url = "{{ url('/') }}";

          notifications();

          $(document).ready(function() {

            $('.notification-bell').click(function() {
              $.ajax({
                  type: 'post'
                  , url: "{{ route('update.notification.count') }}"
                })
                .done(function(response) {
                  $('.notification-bell').hide();
                })

            })
          });


          function notifications() {
            $.ajax({
                type: 'get'
                , url: site_url + '/notifications'
              })
              .done(function(response) {
                $('.dropdown-notifications-ul-list').append(response.data)
              })
              .fail(function(jqXHR, ajaxOptions, thrownError) {
                // alert('No response from server');
              });
          }
    @endauth
  });

</script>

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
