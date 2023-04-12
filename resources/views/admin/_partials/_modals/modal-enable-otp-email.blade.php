<!-- Enable Email OTP Modal -->
<div class="modal fade" id="enableEmailOTP" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="mb-4">
          <h5 class="mb-2">Setup 2FA Using Email</h5>
          <div class="text-gray-500 fw-semibold fs-6">
            <p>An email has been sent to your email address. <br>
              The email contains a verification code, please paste the verification code in the field below.</p>
          </div>
        </div>
        <form class="row g-3" action="{{route('admin.two-factor-email.confirm')}}" method="POST">
          @csrf
          <div class="form-group col-12">
            <label class="form-label" for="modalEnableOTPPhone">Two-factor email authentication code </label>
            <input id="code" placeholder="{{ __('Authentication code') }}" type="text"
            class="form-control @error('code') is-invalid @enderror" name="code" required>
          </div>
          <div class="text-gray-500 fw-semibold fs-6">
            Click the send OTP  button to receive the verification code to your email address, which contains two factor login code for confirmaiton.
          </div>
          <div class="col-12">
            <button type="submit" data-form="ajax-form" class="btn btn-primary ms-2">Submit</button>
            <button  id="resendEmailOTPCode" class="btn btn-label-secondary ms-2">Send OTP</button>
            {{-- <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button> --}}
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Enable Email OTP Modal -->
