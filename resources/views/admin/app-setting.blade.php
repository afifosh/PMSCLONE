@php
$configData = Helper::appClasses();
@endphp

@extends('admin/layouts/layoutMaster')

@section('title', 'Settings')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{asset('assets/css/app-settings.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('assets/js/custom/app-settings.js')}}"></script>
@endsection

@section('content')
    <h4 class="fw-semibold mb-4">@lang('Settings')</h4>
    <div class="app-setting card">
        <div class="row g-0">
            <!-- Settings Sidebar -->
            <div class="col setting-sidebar border-end flex-grow-0" id="setting-sidebar">
                <div class="btn-compost-wrapper d-grid">

                </div>
                <!-- Settings Filters -->
                <div class="setting-filter py-2">
                    <!-- Settings Filters: Folder -->
                    <ul class="setting-list list-unstyled mb-4">
                        <li class="active d-flex justify-content-between py-3" data-target="general">
                            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                                <i class="ti ti-mail"></i>
                                <span class="align-middle ms-2">@lang('General')</span>
                            </a>
                        </li>
                        <li class="d-flex py-3" data-target="email">
                            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                                <i class="ti ti-mail"></i>
                                <span class="align-middle ms-2">@lang('Email Setup')</span>
                            </a>
                        </li>
                        <li class="d-flex py-3" data-target="others">
                            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                                <i class="ti ti-settings"></i>
                                <span class="align-middle ms-2">@lang('Others')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!--/ Settings Sidebar -->

            <!-- Settings List -->
            <div class="col settings-list">
                <div class="shadow-none border-0">
                    <div class="emails-list-header p-3 py-lg-3 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center w-100">
                                <i class="ti ti-menu-2 ti-sm cursor-pointer d-block d-lg-none me-3" data-bs-toggle="sidebar" data-target="#setting-sidebar" data-overlay></i>
                            </div>
                        </div>
                    </div>

                    <div class="setting pt-0 px-5">
                        <div class="setting-item" data-general="true" data-bs-toggle="sidebar">
                            <form method="POST" action="{{ route('admin.setting.store') }}">
                                @csrf

                                <div class="row">
                                    <!-- Password expires after -->
                                    <div class="col-md-6 mb-3">
                                        <label for="passwordExpiryDays" class="form-label fs-6 mb-2 fw-semibold @error('password_expiry_days') is-invalid @enderror">
                                            @lang('Password expires after')
                                        </label>
                                        <input name="password_expire_days" value="{{ $general_settings->password_expire_days ?? config('auth.password_expire_days') }}" type="text" class="form-control" id="passwordExpiryDays" placeholder="{{ __('Number of days') }}" aria-describedby="passwordExpiryDays" />
                                        @error('password_expire_days')
                                        <div class="alert alert-danger alert-dismissible my-2">
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        <div id="defaultFormControlHelp" class="form-text">
                                            @lang('Please input the days after which the password will be expired')
                                        </div>
                                    </div>
                                </div>
                                <!-- Password expires after -->

                                <!-- Password expires after -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="timeoutWarningIn" class="form-label fs-6 mb-2 fw-semibold @error('password_expiry_days') is-invalid @enderror">
                                            @lang('Timeout warning in')
                                        </label>
                                        <input name="timeout_warning_seconds" value="{{ $general_settings->timeout_warning_seconds ?? config('auth.timeout_warning_seconds') }}" type="text" class="form-control" id="timeoutWarningIn" placeholder="{{ __('Timeout warning after x seconds') }}" aria-describedby="timeoutWarningIn" />
                                        @error('timeout_warning_seconds')
                                        <div class="alert alert-danger alert-dismissible my-2">
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        <div id="defaultFormControlHelp" class="form-text">
                                            @lang('Timeout warning will appear after the number of seconds from above')
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="timeoutAfter" class="form-label fs-6 mb-2 fw-semibold @error('password_expiry_days') is-invalid @enderror">
                                            @lang('Timeout after')
                                        </label>
                                        <input name="timeout_after_seconds" value="{{ $general_settings->timeout_after_seconds ?? config('auth.timeout_after_seconds') }}" type="text" class="form-control" id="timeoutAfter" placeholder="{{ __('Timeout after x amount of seconds') }}" aria-describedby="timeoutAfter" />
                                        @error('timeout_after_seconds')
                                        <div class="alert alert-danger alert-dismissible my-2">
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        <div id="defaultFormControlHelp" class="form-text">
                                            @lang('User will be timed out after the number of seconds from above')
                                        </div>
                                    </div>
                                </div>

                                <!-- button to submit form -->
                                <button type="submit" class="btn btn-primary me-sm-3">@lang('Update')</button>
                            </form>
                        </div>

                        <div class="setting-item d-none" data-email="true" data-bs-toggle="sidebar">
                            <form method="POST" action="{{ route('admin.setting.email.upsert') }}">
                                @csrf

                                <div class="row">
                                    @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        {!! implode('<br />', $errors->all('<span>:message</span>')) !!}
                                    </div>
                                    @endif
                                    <!-- Supported mail services -->
                                    <div class="col-md-6 mb-4">
                                        <label for="supportedMailServices" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Supported mail services')
                                        </label>
                                        <select name="service" id="supportedMailServices" value="" class="selectpicker w-100" data-style="btn-default">
                                            @php
                                            $active_email_service = 'ses';
                                            @endphp

                                            @foreach($email_services as $service)
                                            @php
                                            if($service->is_active) $active_email_service = $service->service;
                                            ${$service->service} = $service->emailServiceFields()->pluck('field_value', 'field_name')->toArray();
                                            @endphp
                                            <option value="{{ $service->service }}" data-tokens="{{ $service->service }}" {{ $service->is_active ? 'selected' : '' }}>
                                                {{ __($service->service_label) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="emailSentFromName" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Email sent from name')
                                        </label>
                                        <input name="email_sent_from_name" value="{{ $$active_email_service['email_sent_from_name'] ?? '' }}" type="text" class="form-control" id="emailSentFromName" placeholder="John Doe" aria-describedby="emailSentFromName" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="emailSentFromEmail" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Email sent from email')
                                        </label>
                                        <input name="email_sent_from_email" value="{{ $$active_email_service['email_sent_from_email'] ?? '' }}" type="email" class="form-control" id="emailSentFromEmail" placeholder="@lang('Type email from address')" aria-describedby="emailSentFromEmail" />
                                    </div>
                                </div>

                                <!-- ses email service fields -->
                                <div id="sesEmailService" class="row email-service {{ $active_email_service === 'ses' ? '' : 'd-none' }}">
                                    <div class="col-md-6 mb-4">
                                        <label for="sesHostName" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Host name')
                                        </label>
                                        <input name="ses_host" value="{{ $ses['host'] ?? '' }}" type="text" class="form-control" id="sesHostName" placeholder="@lang('Type host name')" aria-describedby="sesHostName" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="sesAccessKeyId" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Access key id')
                                        </label>
                                        <input name="ses_access_key_id" value="{{ $ses['access_key_id'] ?? '' }}" type="text" class="form-control" id="sesAccessKeyId" placeholder="@lang('Type access key id')" aria-describedby="sesAccessKeyId" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="sesSecretAccessKey" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Secret access key')
                                        </label>
                                        <input name="ses_secret_access_key" value="{{ $ses['secret_access_key'] ?? '' }}" type="text" class="form-control" id="sesSecretAccessKey" placeholder="@lang('Type secret access key')" aria-describedby="sesSecretAccessKey" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="sesRegion" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Region')
                                        </label>
                                        <input name="ses_region" value="{{ $ses['region'] ?? '' }}" type="text" class="form-control" id="sesRegion" placeholder="@lang('Type region')" aria-describedby="sesRegion" />
                                    </div>
                                </div>

                                <!-- Mailgun email service fields -->
                                <div id="mailgunEmailService" class="row email-service {{ $active_email_service === 'mailgun' ? 'd-block' : 'd-none' }}">
                                    <div class="col-md-6 mb-4">
                                        <label for="mailgunDomainName" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Domain name')
                                        </label>
                                        <input name="mailgun_domain_name" value="{{ $mailgun['domain_name'] ?? '' }}" type="text" class="form-control" id="mailgunDomainName" placeholder="@lang('Type domain name')" aria-describedby="mailgunDomainName" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailgunApiKey" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Api key')
                                        </label>
                                        <input name="mailgun_api_key" value="{{ $mailgun['api_key'] ?? '' }}" type="text" class="form-control" id="mailgunApiKey" placeholder="@lang('Type api key')" aria-describedby="mailgunApiKey" />
                                    </div>
                                </div>

                                <!-- Mailgun email service fields -->
                                <div id="smtpEmailService" class="row email-service {{ $active_email_service === 'smtp' ? '' : 'd-none' }}">
                                    <div class="col-md-6 mb-4">
                                        <label for="smtpUsername" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Username')
                                        </label>
                                        <input name="smtp_username" value="{{ $smtp['username'] ?? '' }}" type="text" class="form-control" id="smtpUsername" placeholder="@lang('Type username')" aria-describedby="smtpUsername" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="smtpHost" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('SMTP host')
                                        </label>
                                        <input name="smtp_host" value="{{ $smtp['host'] ?? '' }}" type="text" class="form-control" id="smtpHost" placeholder="@lang('Type SMTP host')" aria-describedby="smtpHost" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="smtpPort" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Port')
                                        </label>
                                        <input name="smtp_port" value="{{ $smtp['port'] ?? '' }}" type="text" class="form-control" id="smtpPort" placeholder="@lang('Type SMTP port')" aria-describedby="smtpPort" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="smtpPasswordToAccess" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Password to access')
                                        </label>
                                        <input name="smtp_password" value="{{ $smtp['password'] ?? '' }}" type="password" class="form-control" id="smtpPasswordToAccess" placeholder="@lang('Type password to access')" aria-describedby="smtpPasswordToAccess" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="smtpEncryptionKey" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Encryption type')
                                        </label>
                                        <select name="smtp_encryption" value="{{ $smtp['encryption'] ?? '' }}" id="smtpEncryptionKey" class="selectpicker w-100" data-style="btn-default">
                                            <option value="">@lang('Choose one')</option>
                                            <option value="tls" {{ isset($smtp['encryption']) && $smtp['encryption'] === 'tls' ? 'selected' : '' }}>@lang('TLS')</option>
                                            <option value="ssl" {{ isset($smtp['encryption']) && $smtp['encryption'] === 'ssl' ? 'selected' : '' }}>@lang('SSL')</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Sendmail email service fields -->
                                <div id="sendmailEmailService" class="row email-service {{ $active_email_service === 'sendmail' ? '' : 'd-none' }}">

                                </div>

                                <!-- Sendmail email service fields -->
                                <div id="mailtrapEmailService" class="row email-service {{ $active_email_service === 'mailtrap' ? '' : 'd-none' }}">
                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapUsername" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Username')
                                        </label>
                                        <input name="mailtrap_username" value="{{ $mailtrap['username'] ?? '' }}" type="text" class="form-control" id="mailtrapUsername" placeholder="@lang('Type username')" aria-describedby="mailtrapUsername" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapHost" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Mailtrap host')
                                        </label>
                                        <input name="mailtrap_host" value="{{ $mailtrap['host'] ?? '' }}" type="text" class="form-control" id="mailtrapHost" placeholder="@lang('Type mailtrap host')" aria-describedby="mailtrapHost" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapPort" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Port')
                                        </label>
                                        <input name="mailtrap_port" value="{{ $mailtrap['port'] ?? '' }}" type="text" class="form-control" id="mailtrapPort" placeholder="@lang('Type mailtrap port')" aria-describedby="mailtrapPort" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapPasswordToAccess" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Password to access')
                                        </label>
                                        <input name="mailtrap_password" value="{{ $mailtrap['password'] ?? '' }}" type="password" class="form-control" id="mailtrapPasswordToAccess" placeholder="@lang('Type password to access')" aria-describedby="mailtrapPasswordToAccess" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapEncryptionKey" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Encryption type')
                                        </label>
                                        <select name="mailtrap_encryption" value="{{ $mailtrap['encryption'] ?? '' }}" id="mailtrapEncryptionKey" class="selectpicker w-100" data-style="btn-default">
                                            <option value="">@lang('Choose one')</option>
                                            <option value="tls" {{ isset($mailtrap['encryption']) && $mailtrap['encryption'] === 'tls' ? 'selected' : '' }}>@lang('TLS')</option>
                                            <option value="ssl" {{ isset($mailtrap['encryption']) && $mailtrap['encryption'] === 'ssl' ? 'selected' : '' }}>@lang('SSL')</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary me-sm-3">@lang('Update')</button>
                            </form>
                        </div>
                    </div>

                </div>
                <div class="app-overlay"></div>
            </div>
            <!-- /Settings List -->
        </div>
    @endsection
