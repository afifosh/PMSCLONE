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
                            <!-- Password expires after -->
                            <div class="col-md-6 mb-4">
                                <label for="passwordExpiryDays" class="form-label fs-6 mb-2 fw-semibold">
                                    @lang('Password expires after')
                                </label>
                                <input name="password_expiry_days" type="text" class="form-control" id="passwordExpiryDays" placeholder="{{ __('Number of days') }}" aria-describedby="passwordExpiryDays" />
                                <div id="defaultFormControlHelp" class="form-text">
                                    @lang('Please input the days after which the password will be expired')
                                </div>
                            </div>
                        </div>

                        <div class="setting-item d-none" data-email="true" data-bs-toggle="sidebar">
                            <!-- Supported mail services -->
                            <div class="col-md-6 mb-4">
                                <label for="supportedMailServices" class="form-label fs-6 mb-2 fw-semibold">
                                    @lang('Supported mail services')
                                </label>
                                <select name="supported_mail_services" id="supportedMailServices" class="selectpicker w-100" data-style="btn-default">
                                    <option value="amazon" data-tokens="amazon">@lang('Amazon SES')</option>
                                    <option value="mailgun" data-tokens="mailgun">@lang('Mailgun')</option>
                                    <option value="smtp" data-tokens="smtp">@lang('SMTP')</option>
                                    <option value="sendmail" data-tokens="sendmail">@lang('Sendmail')</option>
                                    <option value="mailtrap" data-tokens="mailtrap">@lang('Mailtrap')</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="emailSentFromName" class="form-label fs-6 mb-2 fw-semibold">
                                    @lang('Email sent from name')
                                </label>
                                <input name="email_sent_from_name" type="text" class="form-control" id="emailSentFromName" placeholder="John Doe" aria-describedby="emailSentFromName" />
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="emailSentFromEmail" class="form-label fs-6 mb-2 fw-semibold">
                                    @lang('Email sent from email')
                                </label>
                                <input name="email_sent_from_email" type="email" class="form-control" id="emailSentFromEmail" placeholder="@lang('Type email from address')" aria-describedby="emailSentFromEmail" />
                            </div>

                            <!-- Amazon email service fields -->
                            <div id="amazonEmailService" class="email-service d-none">
                                <div class="col-md-6 mb-4">
                                    <label for="amazonHostName" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Host name')
                                    </label>
                                    <input name="amazon_host_name" type="text" class="form-control" id="amazonHostName" placeholder="@lang('Type host name')" aria-describedby="amazonHostName" />
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="amazonAccessKeyId" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Access key id')
                                    </label>
                                    <input name="amazon_access_key_id" type="text" class="form-control" id="amazonAccessKeyId" placeholder="@lang('Type access key id')" aria-describedby="amazonAccessKeyId" />
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="amazonSecretAccessKey" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Secret access key')
                                    </label>
                                    <input name="amazon_secret_access_key" type="text" class="form-control" id="amazonSecretAccessKey" placeholder="@lang('Type secret access key')" aria-describedby="amazonSecretAccessKey" />
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="amazonRegion" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Region')
                                    </label>
                                    <input name="amazon_region" type="text" class="form-control" id="amazonRegion" placeholder="@lang('Type region')" aria-describedby="amazonRegion" />
                                </div>
                            </div>

                            <!-- Mailgun email service fields -->
                            <div id="mailgunEmailService" class="email-service d-none">
                                <div class="col-md-6 mb-4">
                                    <label for="mailgunDomainName" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Domain name')
                                    </label>
                                    <input name="mailgun_domain_name" type="text" class="form-control" id="mailgunDomainName" placeholder="@lang('Type domain name')" aria-describedby="mailgunDomainName" />
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="mailgunApiKey" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Api key')
                                    </label>
                                    <input name="mailgun_api_key" type="text" class="form-control" id="mailgunApiKey" placeholder="@lang('Type api key')" aria-describedby="mailgunApiKey" />
                                </div>
                            </div>

                            <!-- Mailgun email service fields -->
                            <div id="smtpEmailService" class="email-service d-none">
                                <div class="col-md-6 mb-4">
                                    <label for="smtpUsername" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Username')
                                    </label>
                                    <input name="smtp_username" type="text" class="form-control" id="smtpUsername" placeholder="@lang('Type username')" aria-describedby="smtpUsername" />
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="smtpHost" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('SMTP host')
                                    </label>
                                    <input name="smtp_host" type="text" class="form-control" id="smtpHost" placeholder="@lang('Type SMTP host')" aria-describedby="smtpHost" />
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="smtpPort" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Port')
                                    </label>
                                    <input name="smtp_port" type="text" class="form-control" id="smtpPort" placeholder="@lang('Type SMTP port')" aria-describedby="smtpPort" />
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="smtpPasswordToAccess" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Password to access')
                                    </label>
                                    <input name="smtp_password" type="password" class="form-control" id="smtpPasswordToAccess" placeholder="@lang('Type password to access')" aria-describedby="smtpPasswordToAccess" />
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="smtpEncryptionKey" class="form-label fs-6 mb-2 fw-semibold">
                                        @lang('Encryption type')
                                    </label>
                                    <select name="smtp_encryption_key" id="smtpEncryptionKey" class="selectpicker w-100" data-style="btn-default">
                                        <option value="">@lang('Choose one')</option>
                                        <option value="TLS">@lang('TLS')</option>
                                        <option value="SSL">@lang('SSL')</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Sendmail email service fields -->
                            <div id="sendmailEmailService" class="email-service d-none">

                            </div>
                        </div>
                    </div>

                </div>
                <div class="app-overlay"></div>
            </div>
            <!-- /Settings List -->
        </div>
    @endsection