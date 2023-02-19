@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'Home')

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Forgot Password -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">@include('_partials.macros', ['height' => 20, 'withbg' => 'fill: #fff;'])</span>
                                <span
                                    class="app-brand-text demo text-body fw-bold">{{ config('variables.templateName') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1 pt-2">@lang('Password Expired')</h4>
                        <p class="mb-4">@lang('Your password has expired, please change it.')</p>
                        @if (session('status'))
                            <p class="text-success mb-3">{{ session('status') }}</p>
                        @endif
                        <!-- id="formAuthentication"  -->
                        <form class="mb-3" action="{{ route('admin.password.expired.reset') }}" method="POST">
                            @csrf
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    {!! implode('<br/>', $errors->all('<span>:message</span>')) !!}
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="current_password" class="form-label">@lang('Current Password')</label>
                                <input id="current_password" type="password" class="form-control" name="current_password" 
                                    placeholder="{{ __('Enter your current password') }}" autofocus>
                                @error('current_password')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">@lang('New Password')</label>
                                <input id="password" type="password" class="form-control" name="password" 
                                    placeholder="{{ __('Enter new password') }}" autofocus>
                                @error('password')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">@lang('Confirm New Password')</label>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" 
                                    placeholder="{{ __('Confirm New Password') }}" autofocus>
                                @error('password_confirmation')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                @lang('Reset Password')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection