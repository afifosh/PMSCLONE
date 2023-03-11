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
                <div class="emails-list-header px-3 pt-lg-3 pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center w-100">
                            <i class="ti ti-menu-2 ti-sm cursor-pointer d-block d-lg-none me-3" data-bs-toggle="sidebar" data-target="#setting-sidebar" data-overlay></i>
                            <h4 class="mb-0 px-2">@lang('Email Setup')</h4>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="setting pt-0 px-4">
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
                            @include('admin.pages.settings.emails.inc.ses')
                            @include('admin.pages.settings.emails.inc.mailgun')
                            @include('admin.pages.settings.emails.inc.smtp')
                            @include('admin.pages.settings.emails.inc.sendmail')
                            @include('admin.pages.settings.emails.inc.mailtrap')
                            <button type="submit" class="btn btn-primary me-sm-3">@lang('Update')</button>
                        </form>
                    </div>
                </div>

            </div>
            <div class="app-overlay"></div>
        </div>
        <!-- /Settings List -->
    </div>
</div>
@endsection