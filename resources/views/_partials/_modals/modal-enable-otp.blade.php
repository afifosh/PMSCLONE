<!-- Enable OTP Modal -->
<div class="modal fade" id="enableOTP" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
    <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Enable 2FA Auth</h3>
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
        </div>
        <form class="row g-3" action="{{route('two-factor.confirm')}}" method="POST">
          @csrf
          <div class="col-12">
            <label class="form-label" for="modalEnableOTPPhone">Confirmation Code</label>
            <div class="input-group">
              <input id="code" placeholder="{{ __('Authentication code') }}" type="text"
                  class="form-control @error('code') is-invalid @enderror" name="code" required>
            </div>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Enable OTP Modal -->
