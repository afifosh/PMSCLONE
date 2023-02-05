@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', '2FAaa Challenge')

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
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">@include('_partials.macros', ['height' => 20, 'withbg' => 'fill: #fff;'])</span>
                                <span
                                    class="app-brand-text demo text-body fw-bold ms-1">{{ config('variables.templateName') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1 pt-2">{{ __('Please enter your authentication code to login.') }} 🔒</h4>
                        @if (session('status'))
                            <p class="text-success mb-3">{{ session('status') }}</p>
                        @endif
                        <form id="formAuthentication" action="{{ route('admin.two-factor.login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="eamil">Authentication code</label>
                                <div class="input-group input-group-merge">
                                    <input id="code" placeholder="{{ __('Authentication code') }}" type="text"
                                        class="form-control @error('code') is-invalid @enderror" name="code" required
                                        autocomplete="current-code">
                                    @error('code')
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
                                <a href="{{ route('admin.two-factor.login', ['type' => 'recovery-code']) }}">
                                  Use Recover Code
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
