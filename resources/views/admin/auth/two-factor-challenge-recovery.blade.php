@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('admin.layouts/layoutMaster' , ['body_class' => 'authentication'])

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
@endsection

@section('content')
@include('admin._partials.auth-section')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <div class="card">
                  <!-- Start Header -->
                  @include('admin._partials.auth-svg-top')
                 <!-- End Header -->                    
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-mainlogo demo">@include('admin._partials.mainlogo', ['height' => 150, 'withbg' => 'fill: #000;'])</span>
                            </a>
                        </div>
                        <!-- /Logo -->     
                        <h6 class="mb-1 pt-2">{{ __('Please enter your recovery code to login.') }}</h6>
                        <p class="mb-2">Punch in one of the recovery codes you received when setting up two-factor authentication . Note: Each recovery code can only be used once.</p>
                        @if (session('status'))
                            <p class="text-success mb-3">{{ session('status') }}</p>
                        @endif
                        <form id="formAuthentication" action="{{ route('admin.two-factor.login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Recovery code</label>
                                    <a href="{{ route('admin.lost.recoverycode.view') }}">
                                      <small>Forgot Recover code?</small>
                                    </a>
                                  </div>
                                <div class="input-group input-group-merge">
                                    <input id="code" placeholder="{{ __('Recovery code') }}" type="text"
                                        class="form-control @error('code') is-invalid @enderror" name="recovery_code" required
                                        autocomplete="current-code">
                                    @error('recovery_code')
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
                                <a href="{{ route('admin.login') }}">
                                    <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
                                    Cancel
                                </a>
                                <a href="{{ route('admin.two-factor.login', ['type' => 'code']) }}">
                                  Use 2FA Code
                                  <i class="ti ti-chevron-right scaleX-n1-rtl"></i>
                              </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
