@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('layouts/layoutMaster' , ['body_class' => 'authentication'])

@section('title', 'Login')

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
    <script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/fingerprint2/fingerprint2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/ua-parser/ua-parser.min.js') }}"></script>
    <script src="{{ asset('assets/js/login-page/fingerprintJs.js') }}"></script>
    <script async src="{{ asset('assets/vendor/libs/fingerprintJs3/fp.min.js') }}" onload="initFingerprintJS()"></script>
    {{-- <script src="https://cdn.jsdelivr.net/gh/Joe12387/detectIncognito@main/dist/detectIncognito.min.js"></script> --}}
@endsection

@section('content')
@include('_partials.auth-section')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Login -->
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
                        <h6 class="mb-1 pt-2">Welcome to {{ config('variables.templateName') }}!</h6>
                        <p class="mb-4">Please sign-in to your account and start the adventure</p>
                        @if (session('status'))
                            <p class="text-success mb-3">{{ session('status') }}</p>
                        @endif
                        @if (session()->has('inactive-user'))
                          <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              <p class="text-danger mb-3">{{ session('inactive-user') }}</p>
                          </div>
                        @endif
                        <form id="formAuthentication" class="mb-3" action="" method="POST">
                            <input type="hidden" class="form-control" id="fingerprint" name="fingerprint">
                            @csrf
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    {!! implode('<br/>', $errors->all('<span>:message</span>')) !!}
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="email" class="form-label">Email or Username test</label>
                                <input type="text" autocomplete="off" class="form-control" id="email" name="email"
                                    placeholder="Enter your email or username" autofocus>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    <a href="{{ route('password.request') }}">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" autoComplete="new-password"/>
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me">
                                    <label class="form-check-label" for="remember-me">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3" id="login-form">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
@endsection
