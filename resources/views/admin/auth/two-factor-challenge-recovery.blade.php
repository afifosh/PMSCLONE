@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('admin.layouts/layoutMaster')

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
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <div class="card">
                  <!-- Start Header -->
                  <svg style="border-radius: 0.428rem 0.428rem 0 0;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 268.94 38.88" xml:space="preserve">
<path d="M244.07,38.88l4.64-6.58l-11.12-20.9l-57.34,27.47h-19.48l4.54-12.36l15.16,5.02l7.58-20.63l-21.72-7.19l-5.29,14.4  l-15.16-5.02l-7.58,20.63l15.56,5.16l-62.01,0l1.98-29.21l-33.56,6.74l2.18,22.47H50.69c-0.91-3.69-1.89-7.56-2.93-11.58  c-0.28-1.06-0.9-3.44-1.89-6.38c-0.55-1.63-1.19-3.35-2.16-5.35c-0.32-0.66-0.55-1.02-0.8-1.58c-0.52-1.18-1.71-3.41-4.15-5.21  c-0.77-0.57-2.25-1.64-4.4-2.07c-4.08-0.81-7.31,1.31-7.97,1.74c-0.47,0.31-1.49,1.02-2.49,2.22c-0.62,0.74-1.45,1.76-1.89,3.33  c-0.28,1-0.31,1.87-0.28,2.45c2.52,7.67,4.36,13.44,5.07,15.77c0.47,1.57,0.88,3.01,0.88,3.01c0.4,1.44,0.72,2.67,0.96,3.59  C19.09,38.84,9.55,38.86,0,38.88V0h268.94v38.88H244.07z"></path>
</svg>
                 <!-- End Header -->                    
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-mainlogo demo">@include('admin._partials.mainlogo', ['height' => 150, 'withbg' => 'fill: #000;'])</span>
                            </a>
                        </div>
                        <!-- /Logo -->     
                        <h6 class="mb-1 pt-2">{{ __('Please enter your authentication code to login.') }}</h6>
                        @if (session('status'))
                            <p class="text-success mb-3">{{ session('status') }}</p>
                        @endif
                        <form id="formAuthentication" action="{{ route('admin.two-factor.login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="eamil">Recovery coddde</label>
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
