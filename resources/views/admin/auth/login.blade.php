@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('admin.layouts/layoutMaster')

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
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
@endsection

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Login -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">@include('admin._partials.macros', ['height' => 20, 'withbg' => 'fill: #fff;'])</span>
                                <span
                                    class="app-brand-text demo text-body fw-bold ms-1">{{ config('variables.templateName') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1 pt-2">Welcome to {{ config('variables.templateName') }}! 👋</h4>
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
                            @csrf
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    {!! implode('<br/>', $errors->all('<span>:message</span>')) !!}
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="email" class="form-label">Email or Username</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email or username" autofocus>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    <a href="{{ route('admin.password.request') }}">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
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
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>
                        <div class="divider my-4">
                            <div class="divider-text">or</div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <a href="javascript:;" class="btn btn-icon btn-label-facebook me-3">
                                <i class="tf-icons fa-brands fa-facebook-f fs-5"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon btn-label-google-plus me-3">
                                <i class="tf-icons fa-brands fa-google fs-5"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon btn-label-twitter">
                                <i class="tf-icons fa-brands fa-twitter fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
@endsection
