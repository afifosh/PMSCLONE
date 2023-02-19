@php
$customizerHidden = 'customizer-hide';
$pageConfigs = ['myLayout' => 'blank'];
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'Account Locked')

@section('vendor-style')

<!-- Vendor -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('page-style')
<!-- Page -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
<link rel="stylesheet" href="{{ asset(mix('assets/css/auth.css')) }}">
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
<div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-7 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img src="{{ asset('assets/img/illustrations/auth-login-illustration-'.$configData['style'].'.png') }}" alt="auth-login-cover" class="img-fluid my-5 auth-illustration" data-app-light-img="illustrations/auth-login-illustration-light.png" data-app-dark-img="illustrations/auth-login-illustration-dark.png">

                <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="auth-login-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
                <!-- Logo -->
                <div class="app-brand mb-4 d-flex flex-column align-items-center justify-content-center">
                    <a href="{{url('/')}}" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">@include('_partials.macros',["height"=>120,"withbg"=>'fill: #fff;'])</span>
                    </a>
                    <div class="mt-3">
                        <h5 class="mb-1 fs-5 fw-bold text-uppercase">{{ __('Account Locked') }}</h5>
                    </div>

                    <div class="mt-4">
                        <div class="avatar mb-3 lock-screen-avatar">
                            <img src="{{ Auth::user()->avatar }}" alt class="h-auto rounded-circle">
                        </div>
                    </div>

                    <div class="mt-5">
                        <h5 class="fw-bold fs-6">{{ Auth::user()->full_name }}</h5>
                    </div>
                </div>

                <form id="formAuthentication" class="mb-3" action="{{ route('admin.auth.unlock') }}" method="POST">
                    @csrf
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {!! implode('<br />', $errors->all('<span>:message</span>')) !!}
                    </div>
                    @endif

                    <div class="mb-4 form-password-toggle">
                        <div class="input-group input-group-merge mb-1">
                            <input type="password" id="password" class="form-control" name="password" placeholder="@lang('Password..')" aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary d-grid w-100" type="submit">
                            <span class="d-flex align-items-center align-middle">
                                <i class="ti ti-key me-2 ti-sm"></i>
                                <span class="flex-grow-1 align-middle">{{ __('Unlock') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /Login -->
    </div>
</div>
@endsection