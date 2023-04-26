@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('layouts/layoutMaster' , ['body_class' => 'authentication'])

@section('title', '2FA Challenge')

@section('vendor-style')
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        const button = document.querySelector('#resendotp');
       // const button = document.querySelector('#otp');
        let sec = 60;
        let countdown = null;

        const updateButton = () => {
        var RemainingText = `Wait ${sec}s`;
        $("#otp").css("color", "#a8aaae");
        $("#resendEmailOTPCode").css("color", "#a8aaae");
        $("#resendEmailOTPCode").css("pointer-events", "none");     
        $("#otp").text(RemainingText);
         if (sec === 0) {
          clearInterval(countdown);
          sec = 60;
          $("#otp").text("Resend Code");
          $("#otp").css("color", "");
          $("#resendEmailOTPCode").css("color", "");          
          $("#resendEmailOTPCode").css("pointer-events", "");             
          return;
         } 

         sec--;
        }

//         button.onclick = () => {
//             event.preventDefault();
//         button.disabled = true;
//          updateButton();
//          countdown = setInterval(function() {
//            updateButton();
//          }, 1000);
// }
        
        $('a#resendEmailOTPCode').on('click', function () {
          event.preventDefault();
           
          $.ajax({
                          type: "GET",
                          dataType: "json",
                          url: "{{ route('user.send.email.otp') }}",
                          success: function(response) {
                              
                              console.log(response);
      
                              toastr['success']('', response.message, {
                                rtl: isRtl
                              });
                              updateButton();
                             countdown = setInterval(function() {
                                 updateButton();
                             }, 1000);
                           },
                           error: function(response) {
                            var jsonResponse = JSON.parse(response.responseText);
                              console.log(jsonResponse);
      
                              toastr['error']('', jsonResponse.message, {
                                rtl: isRtl
                              });

                           }
                      });    
                           
        });
    });
      </script>
@endsection

@section('content')
@include('_partials.auth-section')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
            <div class="card">
                  <!-- Start Header -->
                  @include('_partials.auth-svg-top')
                 <!-- End Header -->                    
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-mainlogo demo">@include('_partials.mainlogo', ['height' => 150, 'withbg' => 'fill: #000;'])</span>
                            </a>
                        </div>
                        <!-- /Logo -->     
                        <h6 class="mb-1 pt-2">{{ __('Please enter your authentication code to login.') }}</h6>
                        <p class="mb-2">Open the two-factor authentication app on your device to view your authentication code and verify your identity</p>   
                        @if (session('status'))
                            <p class="text-success mb-3">{{ session('status') }}</p>
                        @endif
                        {{ Session::get('login.authenticate_via')  }}
                        <form id="formAuthentication"  action="{{ Session::get('login.authenticate_via') == 'email' ? route('user.check_code') : route('two-factor.login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="eamil">Authentication code</label>
                                <div class="">
                                    <input id="code" placeholder="{{ __('Authentication code') }}" type="text"
                                        class="form-control @error('code') is-invalid @enderror" name="code" required
                                        autocomplete="current-code">
                                    @error('code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <button class="btn btn-primary d-grid w-100 mb-3">
                               Submit
                            </button>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('login') }}">
                                    <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
                                    Cancel
                                </a>
                                @if ( Session::get('login.authenticate_via') == 'google_authenticator')
                                <a href="{{ route('two-factor.login', ['type' => 'recovery-code']) }}">
                                  Use Recovery Code
                                  <i class="ti ti-chevron-right scaleX-n1-rtl"></i>
                                 </a>
                                 @else
                                 {{-- <button class="btn rounded-pill btn-outline-secondary waves-effect " id="resendotp">Resend OTP</button> --}}
                                 <a  href="javascript:;" id="resendEmailOTPCode">
                                    <span id="otp">Resend Code</span>
                                    <i class="ti ti-chevron-right scaleX-n1-rtl"></i>
                                   </a>
                                 @endif
                            </div>                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
