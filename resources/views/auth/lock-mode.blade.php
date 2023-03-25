@php
$customizerHidden = 'customizer-hide';
$pageConfigs = ['myLayout' => 'blank'];
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster' , ['body_class' => 'authentication'])

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
@include('_partials.auth-section')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Account Lock -->
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
                        <h6 class="mb-1 pt-2">{{ __('Account Locked') }}</h6>
                        <p class="mb-4">Enter your  password</p>
                        <div class="text-center">
                        <div class="mt-4">
                              <div class="mx-auto d-block mb-3">
                                <img src="{{ Auth::user()->avatar }}" alt class="h-auto rounded-circle">
                           </div>
                         </div>
                          <div class="mt-2">
                            <h5 class="fw-bold fs-6">{{ Auth::user()->full_name }}</h5>
                         </div>
                         </div>
                        <form id="formAuthentication" class="mb-3" action="{{ route('auth.unlock') }}" method="POST">
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
                <!-- /Account Lock-->
            </div>
        </div>
    </div>
@endsection