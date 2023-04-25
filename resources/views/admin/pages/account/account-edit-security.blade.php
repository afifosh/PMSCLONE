@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'Account settings - Security')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-account-settings.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-account-settings-security.js')}}"></script>
{{-- <script src="{{asset('assets/js/modal-enable-otp.js')}}"></script> --}}
<script>
  /*=========================================================================================
    File Name: ext-component-clipboard.js
    Description: Copy to clipboard
    --------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(document).ready(function () {

var btnCopy = $('.ti-copy'),
  isRtl = $('html').attr('data-textdirection') === 'rtl';

// copy text on click
btnCopy.on('click', function () {
  var tiID = "ti-copy-" + $(this).attr('id');
  let copyText = document.getElementById(tiID); //document.querySelector("p");
    navigator.clipboard.writeText(copyText.innerText).then(function() {
      console.log("Copied to clipboard");
    }, function(err) {
      console.error("Could not copy text: ", err);
    });

  toastr['success']('', 'Copied to clipboard!', {
    rtl: isRtl
  });
});


       const button = document.querySelector('#resendEmailOTPCode');
        let sec = 60;
        let countdown = null;

        const updateButton = () => {
        button.innerHTML = `Wait ${sec}s`;

         if (sec === 0) {
          clearInterval(countdown);
          sec = 60;
          button.innerHTML = 'Resend Code';
          button.disabled = false;    
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
        
        $('#resendEmailOTPCode').on('click', function () {
          event.preventDefault();
           
          $.ajax({
                          type: "GET",
                          dataType: "json",
                          url: "{{ route('admin.send.email.otp') }}",
                          success: function(response) {
                              
                              console.log(response);
      
                              toastr['success']('', response.message, {
                                rtl: isRtl
                              });
                              button.disabled = true;
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
// Send Automated OTP Code to Email on Modal Show
$('#enableEmailOTP').on('show.bs.modal', function (event) {

//   $.ajax({
//                     type: "GET",
//                     dataType: "json",
//                     url: "{{ route('admin.send.email.otp') }}",
//                     success: function(response) {
                        
//                         console.log(response);

//                         toastr['success']('', response.result, {
//                           rtl: isRtl
//                         });

//                     }
//                 });

})

$('#ShowConfirmPassword').on('show.bs.modal', function (event) {

  //alert("btn event");
  var button = $(event.relatedTarget) // Button triggered the modal
  var endpoint = button.data('endpoint')
  var action = button.data('action') 
  //alert(endpoint + "   " + action);
  var modal = $(this)

  modal.find('.modal-body input#two-factor-action').val(action)
  modal.find('.modal-body input#two-factor-endpoint').val(endpoint)

})

});
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Security
</h4>
{{-- {{ dd(session()->all()); }} --}}
<div class="row">
  <div class="col-md-12">
  @include('admin.pages.account._partials.tabs')
    <!-- Change Password -->
    <div class="card mb-4">
      <h5 class="card-header">Change Password</h5>
      <div class="card-body">
        <form method="POST" action="{{route('admin.user-password.update')}}">
          @method('PUT')
          @csrf
          @if ($errors->updatePassword->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {!! implode('<br/>', $errors->updatePassword->all('<span>:message</span>')) !!}
            </div>
          @endif
          <div class="row">
            <div class="mb-3 col-md-6 form-password-toggle">
              <label class="form-label" for="currentPassword">Current Password</label>
              <div class="input-group input-group-merge">
                <input class="form-control" type="password" name="current_password" id="currentPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required/>
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="mb-3 col-md-6 form-password-toggle">
              <label class="form-label" for="newPassword">New Password</label>
              <div class="input-group input-group-merge">
                <input class="form-control" type="password" id="newPassword" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required/>
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
            </div>

            <div class="mb-3 col-md-6 form-password-toggle">
              <label class="form-label" for="confirmPassword">Confirm New Password</label>
              <div class="input-group input-group-merge">
                <input class="form-control" type="password" name="password_confirmation" id="confirmPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required/>
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
            </div>
            <div class="col-12 mb-4">
              <h6>Password Requirements:</h6>
              <ul class="ps-3 mb-0">
                <li class="mb-1">Minimum 8 characters long - the more, the better</li>
                <li class="mb-1">At least one lowercase character</li>
                <li>At least one number, symbol, or whitespace character</li>
              </ul>
            </div>
            <div>
              <button type="submit" class="btn btn-primary me-2">Save changes</button>
              <button type="reset" class="btn btn-label-secondary">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <!--/ Change Password -->

   <!-- Two-steps verification -->
    <div class="card mb-4">
      <h5 class="card-header">Two-steps verification</h5>
      <div class="card-body p-9">
          <!--begin::Notice-->

          <div class="alert alert-secondary alert-dismissible d-flex align-items-baseline notice d-flex bg-light-primary rounded border border-dashed  p-6">
            <!--begin::Icon-->
            <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
            <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        opacity="0.3"
                        d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                        fill="currentColor"
                    ></path>
                    <path
                        d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z"
                        fill="currentColor"
                    ></path>
                </svg>
            </span>
            <!--end::Svg Icon-->
            <!--end::Icon-->
        
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                <!--begin::Content-->
                <div class="mb-3 mb-md-0 fw-semibold">
                    <h5 class="text-gray-900 fw-bold">Secure Your Account</h5>
        
                    <div class="fs-6 text-gray-700 pe-7">Two-factor authentication adds an extra layer of security to your account. To log in, in addition you'll need to provide a 6 digit code</div>
                </div>
                <!--end::Content-->
            </div>
            <!--end::Wrapper-->
        </div>
                  

          <!--end::Notice-->

          <!--begin::Items-->
          <div class="py-2">
              <!--begin::Item-->
              <div class="d-flex flex-stack">

                <div class="d-flex">
                  <svg style="width: 30px;" class="me-4 col-md-1 d-flex justify-content-center align-self-baseline w-30 me-4" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 2481.9 2481.9" style="enable-background:new 0 0 2481.9 2481.9;" xml:space="preserve">
                    <style type="text/css">
                      .st0{fill:#616161;}
                      .st1{fill:#9E9E9E;}
                      .st2{fill:#424242;}
                      .st3{fill:#212121;fill-opacity:0.1;}
                      .st4{fill:#FFFFFF;fill-opacity:5.000000e-02;}
                      .st5{opacity:0.5;fill:#BDBDBD;enable-background:new    ;}
                      .st6{fill:#BDBDBD;}
                      .st7{fill:#757575;}
                      .st8{fill:#FFFFFF;fill-opacity:0.2;}
                      .st9{fill:#212121;fill-opacity:0.2;}
                      .st10{fill:url(#SVGID_1_);}
                    </style>
                    <g transform="translate(-27 -27)">
                      <circle class="st0" cx="1268" cy="1268" r="1241"/>
                      <path class="st1" d="M1268,2057.7c-436.2,0-789.7-353.5-789.7-789.7c0-436.2,353.5-789.7,789.7-789.7c218,0,415.4,88.4,558.4,231.3   l319.1-319.1C1920.9,165.9,1610.6,27,1268,27C582.6,27,27,582.6,27,1268s555.6,1241,1241,1241c342.7,0,652.9-138.9,877.6-363.4   l-319.1-319.1C1683.4,1969.3,1486,2057.7,1268,2057.7z"/>
                      <path class="st2" d="M2057.7,1268h-394.9c0-218-176.8-394.9-394.9-394.9S873.1,1049.9,873.1,1268c0,106.2,42,202.5,110.3,273.6   l-0.3,0.3l488.9,488.9l0.1,0.1C1809.3,1940.9,2057.7,1633.5,2057.7,1268L2057.7,1268z"/>
                      <path class="st0" d="M2508.9,1268h-451.3c0,365.5-248.5,672.9-585.5,762.9l348.5,348.5C2228.6,2176.1,2508.9,1754.8,2508.9,1268z"/>
                      <path class="st3" d="M1268,2494.8C585,2494.8,31,1943,27.1,1260.9c0,2.4-0.1,4.7-0.1,7.1c0,685.4,555.6,1241,1241,1241   s1241-555.6,1241-1241c0-2.4-0.1-4.7-0.1-7.1C2505,1943,1950.9,2494.8,1268,2494.8L1268,2494.8z"/>
                      <path class="st4" d="M1472.2,2030.9l11,11c331.4-93.6,574.5-398.2,574.5-759.8V1268C2057.7,1633.5,1809.2,1940.9,1472.2,2030.9   L1472.2,2030.9z"/>
                      <g transform="translate(236.455 236.455)">
                        <path class="st1" d="M2046.9,918.7H1031.5c-62.3,0-112.8,50.5-112.8,112.8c0,62.3,50.5,112.8,112.8,112.8h1015.3    c62.3,0,112.8-50.5,112.8-112.8C2159.7,969.2,2109.2,918.7,2046.9,918.7z"/>
                        <path class="st5" d="M2046.9,918.7H1031.5c-62.3,0-112.8,50.5-112.8,112.8c0,62.3,50.5,112.8,112.8,112.8h1015.3    c62.3,0,112.8-50.5,112.8-112.8C2159.7,969.2,2109.2,918.7,2046.9,918.7z"/>
                      </g>
                      <g>
                        <g>
                          <circle class="st6" cx="252.6" cy="1268" r="84.6"/>
                          <circle class="st6" cx="1268" cy="252.6" r="84.6"/>
                          <circle class="st6" cx="1268" cy="2283.3" r="84.6"/>
                          <circle class="st6" cx="548.8" cy="550" r="84.6"/>
                          <circle class="st6" cx="548.8" cy="1987.2" r="84.6"/>
                        </g>
                        <circle class="st7" cx="1987.2" cy="1987.2" r="84.6"/>
                        <path class="st8" d="M1268,1169.3h1015.3c59.9,0,108.9,46.8,112.4,105.8c0.1-2.4,0.4-4.7,0.4-7.1c0-62.3-50.5-112.8-112.8-112.8    H1268c-62.3,0-112.8,50.5-112.8,112.8c0,2.4,0.1,4.7,0.4,7.1C1159.1,1216.1,1208,1169.3,1268,1169.3z"/>
                        <path class="st9" d="M2395.7,1275c-3.7,58.9-52.6,105.8-112.4,105.8H1268c-59.9,0-108.9-46.8-112.4-105.8c-0.3,2.4-0.4,4.7-0.4,7    c0,62.3,50.5,112.8,112.8,112.8h1015.3c62.3,0,112.8-50.5,112.8-112.8C2396.1,1279.7,2396,1277.4,2395.7,1275z"/>
                        <path class="st3" d="M1268,492.4c218,0,415.4,88.4,558.4,231.3l326-326.2c-2.4-2.4-4.7-4.8-7-7.1l-319,319.1    c-143-142.9-340.4-231.3-558.4-231.3c-436.2,0-789.7,353.5-789.7,789.7c0,2.4,0.1,4.7,0.1,7.1C482.2,842.1,834.2,492.4,1268,492.4    L1268,492.4z"/>
                        
                          <radialGradient id="SVGID_1_" cx="706.7253" cy="1774.0293" r="0.9983" gradientTransform="matrix(2481.9333 0 0 -2481.9333 -1753654.125 4403410.5)" gradientUnits="userSpaceOnUse">
                          <stop offset="0" style="stop-color:#FFFFFF;stop-opacity:0.1"/>
                          <stop offset="1" style="stop-color:#FFFFFF;stop-opacity:0"/>
                        </radialGradient>
                        <circle class="st10" cx="1268" cy="1268" r="1241"/>
                      </g>
                    </g>
                    </svg>

                        <div class="d-flex-disable flex-column-disable">
                          <a href="#" class="fs-5 text-dark text-hover-primary fw-bold">Setup Using Google Authenticator

            

                            @if (auth()->user()->two_factor_confirmed_at && auth()->user()->two_factor_secret)
                              <span class="ms-2 badge rounded-pill bg-label-success">Active</span>
                            @elseif (auth()->user()->two_factor_secret)  
                              <span class="ms-2 badge rounded-pill bg-label-warning">Pending</span>
                            @endif

                          </a>
                          <div class="mt-4 fs-6 fw-semibold text-gray-400">
                            Get codes from an app like Google Authenticator,  Microsoft Authenticator, Authy or 1Password.
                        </div>

                        <div class="text-gray-500 fw-semibold fs-6">
                          Using an authenticator app like
                          <a href="https://support.google.com/accounts/answer/1066447?hl=en" target="_blank">Google Authenticator</a>,
                          <a href="https://www.microsoft.com/en-us/account/authenticator" target="_blank">Microsoft Authenticator</a>,
                          scan the QR code. It will generate a 6 digit code for you to enter below.    
                           </div>
                           @if (auth()->user()->two_factor_secret)         
                            <div class="p-4">
                                  {!! auth()->user()->twoFactorQrCodeSvg() !!}
                          </div>
                          @endif        
                          @if (auth()->user()->two_factor_secret)
                          <div class="alert alert-primary alert-dismissible d-flex align-items-baseline" role="alert">
                            <span class="alert-icon alert-icon-lg text-primary me-2">
                              <i class="ti ti-bell ti-xs"></i>
                            </span>  
                            <div class="d-flex flex-column ps-1">        
                              <div class="fs-6 text-gray-700 ">If you having trouble using the QR code, select manual entry on your app, and enter your username and the code: <div class="fw-bold text-dark pt-2">{!!  decrypt(auth()->user()->two_factor_secret)  !!}</div></div>
                            </div>
                          </div>
                          @endif
                            <p>Please Scan The above code to get the Confirmation Code</p>
                   


                        @if (auth()->user()->two_factor_secret)
                        @if (auth()->user()->two_factor_confirmed_at)
                        <button type="button" class="btn btn-primary" data-action="disable" data-endpoint="two-factor-authentication"  data-bs-toggle="modal" data-bs-target="#ShowConfirmPassword">Disable</button>
                        <button type="button" class="btn btn-danger" data-action="download_code" data-endpoint="two-factor-authentication"  data-bs-toggle="modal" data-bs-target="#ShowConfirmPassword">Download Recovery Codes</button>
                        <button type="button" class="btn btn-danger" data-action="regenerate_code" data-endpoint="two-factor-authentication"  data-bs-toggle="modal" data-bs-target="#ShowConfirmPassword">Regenerate Recovery Codes</button>
  


                                    @else
                      <h6 class="fw-semibold mb-3">Two factor authentication is not Confirmed yet.</h6>
                                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#enableOTP">Confirm Two Factor Auth</button>
                                      <button type="button" class="btn btn-primary" data-action="disable" data-endpoint="two-factor-authentication"  data-bs-toggle="modal" data-bs-target="#ShowConfirmPassword">Disable</button>
                                    @endif
                                @else
                                     <button type="button" class="btn btn-primary" data-action="enable" data-endpoint="two-factor-authentication"  data-bs-toggle="modal" data-bs-target="#ShowConfirmPassword">Enable</button>
                                    {{-- <form method="POST" action="{{ '/admin/user/two-factor-authentication' }}">
                                      @csrf
                                      <button class="btn btn-primary mt-2">Enable</button>
                                    </form> --}}
                                @endif

                      </div>
                  </div>
                  {{-- <div class="d-flex justify-content-end">
                      <div class="form-check form-check-solid form-check-custom form-switch">
                          <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch"/>
                          <label class="form-check-label" for="googleswitch"></label>
                      </div>
                  </div> --}}
              </div>
              <!--end::Item-->

              <div class="separator separator-dashed my-3"></div>

              <!--begin::Item-->
              <div class="d-flex flex-stack">
                  <div class="d-flex">

                    <svg aria-hidden="true" focusable="false" data-prefix="fa" data-icon="envelope-open-text" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="" style="width: 30px;" class="me-4 col-md-1 d-flex justify-content-center align-self-baseline w-30 me-4 svg-inline--fa fa-envelope-open-text fa-w-16 f-27 text-lightest"><path fill="currentColor" d="M176 216h160c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16H176c-8.84 0-16 7.16-16 16v16c0 8.84 7.16 16 16 16zm-16 80c0 8.84 7.16 16 16 16h160c8.84 0 16-7.16 16-16v-16c0-8.84-7.16-16-16-16H176c-8.84 0-16 7.16-16 16v16zm96 121.13c-16.42 0-32.84-5.06-46.86-15.19L0 250.86V464c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V250.86L302.86 401.94c-14.02 10.12-30.44 15.19-46.86 15.19zm237.61-254.18c-8.85-6.94-17.24-13.47-29.61-22.81V96c0-26.51-21.49-48-48-48h-77.55c-3.04-2.2-5.87-4.26-9.04-6.56C312.6 29.17 279.2-.35 256 0c-23.2-.35-56.59 29.17-73.41 41.44-3.17 2.3-6 4.36-9.04 6.56H96c-26.51 0-48 21.49-48 48v44.14c-12.37 9.33-20.76 15.87-29.61 22.81A47.995 47.995 0 0 0 0 200.72v10.65l96 69.35V96h320v184.72l96-69.35v-10.65c0-14.74-6.78-28.67-18.39-37.77z"></path></svg>

                      <div class="d-flex-disable flex-column-disable">
                          <a href="#" class="fs-5 text-dark text-hover-primary fw-bold">Setup Using Email
                          @if (Auth::user()->two_factor_email_confirmed)
                          <span class="ms-2 badge rounded-pill bg-label-success">Active</span>
                          @endif
                         </a>
                          <div class="mt-4 mb-2 fs-6 fw-semibold text-gray-400">Enabling this feature will send code on your email account  {{ Auth::user()->email }} for log in.</div>
                          @if (Auth::user()->two_factor_email_confirmed)
                          <button type="button" class="btn btn-primary"  data-action="disable" data-endpoint="two-factor-email-authentication" data-bs-toggle="modal" data-bs-target="#ShowConfirmPassword">Disable</button>
                          @else
                          <button type="button" class="btn btn-primary"  data-action="enable" data-endpoint="two-factor-email-authentication"  data-bs-toggle="modal" data-bs-target="#enableEmailOTP">Enable</button>           
                          @endif
                      </div>
                  </div>

         
              </div>
              <!--end::Item-->

              <div class="separator separator-dashed my-3"></div>

          </div>
          <!--end::Items-->
      </div>
    </div>

    <!-- Modal -->
    @include('admin/_partials/_modals/modal-enable-otp')
    @include('admin/_partials/_modals/modal-enable-otp-email')
    @include('admin/_partials/_modals/modal-otp-confirm-password')    
    <!-- /Modal -->


 
    <!--/ Two-steps verification -->

    <!-- Create an API key -->
   {{-- <div class="card mb-4">
      <h5 class="card-header">Create an API key</h5>
      <div class="row">
        <div class="col-md-5 order-md-0 order-1">
          <div class="card-body">
            <form id="formAccountSettingsApiKey" method="POST" onsubmit="return false">
              <div class="row">
                <div class="mb-3 col-12">
                  <label for="apiAccess" class="form-label">Choose the Api key type you want to create</label>
                  <select id="apiAccess" class="select2 form-select">
                    <option value="">Choose Key Type</option>
                    <option value="full">Full Control</option>
                    <option value="modify">Modify</option>
                    <option value="read-execute">Read & Execute</option>
                    <option value="folders">List Folder Contents</option>
                    <option value="read">Read Only</option>
                    <option value="read-write">Read & Write</option>
                  </select>
                </div>
                <div class="mb-3 col-12">
                  <label for="apiKey" class="form-label">Name the API key</label>
                  <input type="text" class="form-control" id="apiKey" name="apiKey" placeholder="Server Key 1" />
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary me-2 d-grid w-100">Create Key</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-7 order-md-1 order-0">
          <div class="text-center mt-4 mx-3 mx-md-0">
            <img src="{{asset('assets/img/illustrations/girl-with-laptop.png')}}" class="img-fluid" alt="Api Key Image" width="202">
          </div>
        </div>
      </div>
    </div> --}}
    <!--/ Create an API key -->

    <!-- Recent Devices -->
    <div class="card mb-4">
      <h5 class="card-header">Recent Devices</h5>
      <h5 class="card-header">Two-steps verification</h5>
      <div class="table-responsive">
        <table class="table border-top">
          <thead class="table-light">
            <tr>
              <th class="text-truncate">Browser</th>
              <th class="text-truncate">Device</th>
              <th class="text-truncate">Location</th>
              <th class="text-truncate">Recent Activities</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr>
              <td class="text-truncate"><i class='ti ti-brand-windows text-info me-2 ti-sm'></i> <strong>Chrome on Windows</strong></td>
              <td class="text-truncate">HP Spectre 360</td>
              <td class="text-truncate">Switzerland</td>
              <td class="text-truncate">10, July 2021 20:07</td>
            </tr>
            <tr>
              <td class="text-truncate"><i class='ti ti-device-mobile text-danger me-2 ti-sm'></i> <strong>Chrome on iPhone</strong></td>
              <td class="text-truncate">iPhone 12x</td>
              <td class="text-truncate">Australia</td>
              <td class="text-truncate">13, July 2021 10:10</td>
            </tr>
            <tr>
              <td class="text-truncate"><i class='ti ti-brand-android text-success me-2 ti-sm'></i> <strong>Chrome on Android</strong></td>
              <td class="text-truncate">Oneplus 9 Pro</td>
              <td class="text-truncate">Dubai</td>
              <td class="text-truncate">14, July 2021 15:15</td>
            </tr>
            <tr>
              <td class="text-truncate"><i class='ti ti-brand-apple me-2 ti-sm'></i> <strong>Chrome on MacOS</strong></td>
              <td class="text-truncate">Apple iMac</td>
              <td class="text-truncate">India</td>
              <td class="text-truncate">16, July 2021 16:17</td>
            </tr>
            <tr>
              <td class="text-truncate"><i class='ti ti-brand-windows text-info me-2 ti-sm'></i> <strong>Chrome on Windows</strong></td>
              <td class="text-truncate">HP Spectre 360</td>
              <td class="text-truncate">Switzerland</td>
              <td class="text-truncate">20, July 2021 21:01</td>
            </tr>
            <tr>
              <td class="text-truncate"><i class='ti ti-brand-android text-success me-2 ti-sm'></i> <strong>Chrome on Android</strong></td>
              <td class="text-truncate">Oneplus 9 Pro</td>
              <td class="text-truncate">Dubai</td>
              <td class="text-truncate">21, July 2021 12:22</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!--/ Recent Devices -->

  </div>
</div>

@endsection