@php
    $customizerHidden = 'customizer-hide';
    $pageConfigs = ['myLayout' => 'blank'];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Verify Email')

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('content')
    <div class="authentication-wrapper authentication-basic px-4">
        <div class="authentication-inner py-4">
            <!-- Verify Email -->
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
                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ __('A new verification link has been sent to your registered email address.') }}
                        </div>
                    @endif
                    <h4 class="mb-1 pt-2">Verify your email ✉️</h4>
                    <p class="text-start mb-4">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on
                        the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another
                    </p>
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-primary w-100 mb-3">{{ __('Resend Verification Email') }}</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-lg btn-light-primary fw-bolder me-4">{{ __('Log out') }}</button>
                    </form>
                </div>
            </div>
            <!-- /Verify Email -->
        </div>
    </div>
@endsection
