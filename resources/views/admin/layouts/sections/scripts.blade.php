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
<script src="{{ asset('js/bootstrap-session-timeout.js') }}"></script>

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

    @auth
      @if(config('fortify.guard') === 'admin' && Route::currentRouteName() !== 'admin.auth.lock')
        $.sessionTimeout({
          keepAliveUrl: '/admin/keep-alive',
          logoutUrl: '/admin/logout',
          redirUrl: '/admin/auth/lock',
          warnAfter: +"{{ config('auth.timeout_warning_seconds') }}",
          redirAfter: +"{{ config('auth.timeout_after_seconds') }}",
          countdownBar: true,
          countdownMessage: 'Redirecting in {timer} seconds.',
          useLocalStorageSynchronization: true,
          ignoreUserActivity: true,
          clearWarningOnUserActivity: false,
        });
      @endif
    var site_url = "{{ url('/') }}";
    var page = 1;

    notifications(page);

    $(document).ready(function() {
      $('.view-more-li.load-more').click(function() {
        page++;
        notifications(page);
      });

      $('.notification-bell').click(function() {
        $.ajax({
            type: 'post'
            , url: "{{ route('admin.update.notification.count') }}"
          })
          .done(function(response) {
            console.log('done');
            $('.notification-bell').hide();
          })

      })
    });


    function notifications(page) {
      $.ajax({
          type: 'get'
          , url: site_url + '/admin/notifications?' + 'page=' + page
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
