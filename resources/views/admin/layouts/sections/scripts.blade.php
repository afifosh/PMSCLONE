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
  const broadcastKey = "{{ config('broadcasting.connections.pusher.key') }}"
  const broadcastCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}"
  const broadcastHost = "{{ config('broadcasting.connections.pusher.options.host') }}"
  const broadcastPort = "{{ config('broadcasting.connections.pusher.options.port') }}"
  const broadcastForceTLS = '{{ config('broadcasting.connections.pusher.options.host') ? 0 : 1 }}'
</script>
<script src="{{ asset(mix('assets/vendor/js/Echo.js')) }}"></script>
<script>
  Echo.private('App.Models.Admin.{{ auth()->id() }}')
    .notification((notification) => {
      $('#unread-notifications-count').show();
      notificationSound();
    });
</script>

<script>
  setInterval(function() {
    console.log('Refreshing CSRF Token');
    $.ajax({
        url: '{{ route("refresh-csrf") }}',
        type: 'get'
    }).done(function (data) {
        $('[name="_token"]').val(data);
    }).fail(function () {
        console.log('Could not refresh CSRF token');
    });
}, 1000 * 55 * {{ config('session.lifetime') > session_cache_expire() ? session_cache_expire() : config('session.lifetime') }});
</script>

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
 @stack('scripts')
 @auth
 @if(Route::currentRouteName() !== 'admin.auth.lock' && config('auth.enable_timeout'))
 <script src="{{ asset(mix('assets/js/bootstrap-session-timeout.js')) }}"></script>
 <script>
   const keepAliveUrl = "{{ route('admin.alive') }}"
   const logoutUrl = "{{ route('admin.logout') }}"
   const warnAfter = +"{{ config('auth.timeout_warning_seconds') }}"
   const redirAfter = +"{{ config('auth.timeout_after_seconds') }}" + warnAfter
 </script>
 <script src="{{ asset(mix('assets/js/custom/session-timeout.js')) }}"></script>
 @endif
 @endauth
<script src="{{ asset(mix('assets/js/custom/bell-notifications.js')) }}"></script>

