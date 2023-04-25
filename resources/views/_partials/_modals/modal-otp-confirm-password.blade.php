<!-- Enable ShowConfirmPassword Modal -->
<div class="modal fade" id="ShowConfirmPassword" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h5 class="mb-2">Password Confirmation</h5>      
          <p>Please enter your password to confirm this change</p>
        </div>
        {{-- <form class="row g-3" action="{{route('admin.two-factor-email.disable')}}" method="POST"> --}}
          <form id="enableOTPForm" class="row g-3" action="{{route('user.security.setting',['active_tab'=>'two-factor-authentication']) }}" method="POST">          
          @csrf
          <input type="hidden" id="two-factor-action" name="action" value="">
          <input type="hidden" id="two-factor-endpoint" name="endpoint" value="">
          @if (auth()->user()->two_factor_email_confirmed && auth()->user()->two_factor_email_confirmed_at)
          {{-- @method('DElETE') --}}
          @endif
          
            {{-- <div class="mb-3 col-md-12 form-password-toggle">
              <label class="form-label" for="currentPassword">Current Password</label>
              <div class="input-group input-group-merge">
                <input class="form-control @error('code') is-invalid @enderror"  type="password" name="password" id="Password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required/>
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
            </div> --}}
            <div class="form-group col-12">
              <label class="form-label" for="Password">Current Password</label>
              <div class="input-group input-group-merge">
              <input id="password" placeholder="{{ __('Current Password') }}" type="text"
              class="form-control @error('password') is-invalid @enderror" name="password" required>
              <span class="input-group-text cursor-pointer @error('password') is-invalid @enderror"><i class="ti ti-eye-off"></i></span>
            </div>
            </div>
          <div class="col-12">
            <button type="submit" data-form="ajax-form" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Enable ShowConfirmPassword Modal -->
