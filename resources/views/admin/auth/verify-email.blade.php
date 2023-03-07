@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('admin.layouts/layoutMaster' , ['body_class' => 'authentication'])

@section('title', 'Verify Email')

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('content')
@include('admin._partials.auth-section')
    <div class="authentication-wrapper authentication-basic px-4">
        <div class="authentication-inner py-4">           
            <!-- Verify Email -->
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
                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ __('A new verification link has been sent to your registered email address.') }}
                        </div>
                    @endif                  
                    <h6 class="mb-1 pt-2">Verify your email</h6>
                    <p class="text-start mb-4">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on
                        the link we just emailed to you? If you didn't receive the email, we will gladly send you another
                    </p>
                    <form method="POST" action="{{ route('admin.verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-primary w-100 mb-3">{{ __('Resend Verification Email') }}</button>
                    </form>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-outline-primary w-100 waves-effect me-4">{{ __('Log out') }}</button>
                    </form>
                </div>
            </div>
            <!-- /Verify Email -->
        </div>
    </div>
@endsection
