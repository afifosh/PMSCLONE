@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.header')

@section('content')
    <h4 class="fw-semibold mb-4">@lang('Settings')</h4>
    <div class="app-setting card">
        <div class="row g-0">
            @include('admin.pages.settings.inc.tabs')

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
                        <div class="setting-item">
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
                                        <select name="name" id="supportedMailServices" value="" class="selectpicker w-100" data-style="btn-default">
                                            @php
                                                $active_service = null;
                                            @endphp

                                            @foreach($emailServices as $service)
                                                @php
                                                    ${$service->name} = $service;
                                                    if($service->is_active) $active_service = $service;
                                                @endphp
                                                <option value="{{ $service->name }}" data-tokens="{{ $service->name }}" {{ $service->is_active ? 'selected' : '' }}>
                                                    {{ __($service->label) }}
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
                                        <input name="sent_from_name" value="{{ $active_service->sent_from_name ?? '' }}" type="text" class="form-control" id="emailSentFromName" placeholder="John Doe" aria-describedby="emailSentFromName" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="emailSentFromEmail" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Email sent from email')
                                        </label>
                                        <input name="sent_from_address" value="{{ $active_service->sent_from_address ?? '' }}" type="email" class="form-control" id="emailSentFromEmail" placeholder="@lang('Type email from address')" aria-describedby="emailSentFromEmail" />
                                    </div>
                                </div>

                                <!-- ses email service fields -->
                                <div id="sesEmailService" class="row email-service {{ $active_service->name === 'ses' ? '' : 'd-none' }}">
                                    <div class="col-md-6 mb-4">
                                        <label for="sesHostName" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Host name')
                                        </label>
                                        <input name="ses_host" value="{{ $ses->host ?? '' }}" type="text" class="form-control" id="sesHostName" placeholder="@lang('Type host name')" aria-describedby="sesHostName" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="sesAccessKeyId" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Access key id')
                                        </label>
                                        <input name="ses_access_key_id" value="{{ $ses->access_key_id ?? '' }}" type="text" class="form-control" id="sesAccessKeyId" placeholder="@lang('Type access key id')" aria-describedby="sesAccessKeyId" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="sesSecretAccessKey" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Secret access key')
                                        </label>
                                        <input name="ses_secret_access_key" value="{{ $ses->secret_access_key ?? '' }}" type="text" class="form-control" id="sesSecretAccessKey" placeholder="@lang('Type secret access key')" aria-describedby="sesSecretAccessKey" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="sesRegion" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Region')
                                        </label>
                                        <input name="ses_region" value="{{ $ses->region ?? '' }}" type="text" class="form-control" id="sesRegion" placeholder="@lang('Type region')" aria-describedby="sesRegion" />
                                    </div>
                                </div>

                                <!-- Mailgun email service fields -->
                                <div id="mailgunEmailService" class="row email-service {{ $active_service->name === 'mailgun' ? 'd-block' : 'd-none' }}">
                                    <div class="col-md-6 mb-4">
                                        <label for="mailgunDomainName" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Domain name')
                                        </label>
                                        <input name="mailgun_domain_name" value="{{ $mailgun->domain_name ?? '' }}" type="text" class="form-control" id="mailgunDomainName" placeholder="@lang('Type domain name')" aria-describedby="mailgunDomainName" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailgunApiKey" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Api key')
                                        </label>
                                        <input name="mailgun_api_key" value="{{ $mailgun->api_key ?? '' }}" type="text" class="form-control" id="mailgunApiKey" placeholder="@lang('Type api key')" aria-describedby="mailgunApiKey" />
                                    </div>
                                </div>

                                <!-- Mailgun email service fields -->
                                <div id="smtpEmailService" class="row email-service {{ $active_service->name === 'smtp' ? '' : 'd-none' }}">
                                    <div class="col-md-6 mb-4">
                                        <label for="smtpUsername" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Username')
                                        </label>
                                        <input name="smtp_username" value="{{ $smtp->username ?? '' }}" type="text" class="form-control" id="smtpUsername" placeholder="@lang('Type username')" aria-describedby="smtpUsername" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="smtpHost" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('SMTP host')
                                        </label>
                                        <input name="smtp_host" value="{{ $smtp->host ?? '' }}" type="text" class="form-control" id="smtpHost" placeholder="@lang('Type SMTP host')" aria-describedby="smtpHost" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="smtpPort" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Port')
                                        </label>
                                        <input name="smtp_port" value="{{ $smtp->port ?? '' }}" type="text" class="form-control" id="smtpPort" placeholder="@lang('Type SMTP port')" aria-describedby="smtpPort" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="smtpPasswordToAccess" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Password to access')
                                        </label>
                                        <input name="smtp_password" value="{{ $smtp->password ?? '' }}" type="password" class="form-control" id="smtpPasswordToAccess" placeholder="@lang('Type password to access')" aria-describedby="smtpPasswordToAccess" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="smtpEncryptionKey" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Encryption type')
                                        </label>
                                        <select name="smtp_encryption" value="{{ $smtp->encryption ?? '' }}" id="smtpEncryptionKey" class="selectpicker w-100" data-style="btn-default">
                                            <option value="">@lang('Choose one')</option>
                                            <option value="tls" {{ isset($smtp->encryption) && $smtp->encryption === 'tls' ? 'selected' : '' }}>@lang('TLS')</option>
                                            <option value="ssl" {{ isset($smtp->encryption) && $smtp->encryption === 'ssl' ? 'selected' : '' }}>@lang('SSL')</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Sendmail email service fields -->
                                <div id="sendmailEmailService" class="row email-service {{ $active_service->name === 'sendmail' ? '' : 'd-none' }}">

                                </div>

                                <!-- Sendmail email service fields -->
                                <div id="mailtrapEmailService" class="row email-service {{ $active_service->name === 'mailtrap' ? '' : 'd-none' }}">
                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapUsername" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Username')
                                        </label>
                                        <input name="mailtrap_username" value="{{ $mailtrap->username ?? '' }}" type="text" class="form-control" id="mailtrapUsername" placeholder="@lang('Type username')" aria-describedby="mailtrapUsername" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapHost" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Mailtrap host')
                                        </label>
                                        <input name="mailtrap_host" value="{{ $mailtrap->host ?? '' }}" type="text" class="form-control" id="mailtrapHost" placeholder="@lang('Type mailtrap host')" aria-describedby="mailtrapHost" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapPort" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Port')
                                        </label>
                                        <input name="mailtrap_port" value="{{ $mailtrap->port ?? '' }}" type="text" class="form-control" id="mailtrapPort" placeholder="@lang('Type mailtrap port')" aria-describedby="mailtrapPort" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapPasswordToAccess" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Password to access')
                                        </label>
                                        <input name="mailtrap_password" value="{{ $mailtrap->password ?? '' }}" type="password" class="form-control" id="mailtrapPasswordToAccess" placeholder="@lang('Type password to access')" aria-describedby="mailtrapPasswordToAccess" />
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="mailtrapEncryptionKey" class="form-label fs-6 mb-2 fw-semibold">
                                            @lang('Encryption type')
                                        </label>
                                        <select name="mailtrap_encryption" value="{{ $mailtrap->encryption ?? '' }}" id="mailtrapEncryptionKey" class="selectpicker w-100" data-style="btn-default">
                                            <option value="">@lang('Choose one')</option>
                                            <option value="tls" {{ isset($mailtrap->encryption) && $mailtrap->encryption === 'tls' ? 'selected' : '' }}>@lang('TLS')</option>
                                            <option value="ssl" {{ isset($mailtrap->encryption) && $mailtrap->encryption === 'ssl' ? 'selected' : '' }}>@lang('SSL')</option>
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
