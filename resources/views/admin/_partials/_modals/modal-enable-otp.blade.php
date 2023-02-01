<!-- Enable OTP Modal -->
<div class="modal fade" id="enableOTP" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">Enable 2FA Auth</h3>
          <div class="pb-5">
            @if (auth()->user()->two_factor_secret)
                {!! auth()->user()->twoFactorQrCodeSvg() !!}
            @endif

        </div>
          <p>Please Scan The above code to get the Confirmation Code</p>
        </div>
        <form class="row g-3" action="{{route('admin.two-factor.confirm')}}" method="POST">
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
