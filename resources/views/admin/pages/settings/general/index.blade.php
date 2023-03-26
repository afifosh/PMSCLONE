@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.header', ['title' => 'General Settings'])

@section('content')
<h4 class="fw-semibold mb-4">@lang('Settings')</h4>
<div class="app-setting card">
    <div class="row g-0">
        @include('admin.pages.settings.inc.tabs')

        <!-- Settings List -->
        <div class="col settings-list">
            <div class="shadow-none border-0">
                @include('admin.pages.settings.inc.card-header', ['heading' => 'General'])
                <hr>
                <div class="setting pt-0 px-4">
                    <div class="setting-item" data-general="true" data-bs-toggle="sidebar">
                        <!-- form -->
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.setting.general.update') }}" class="mt-3" id="general-setting-form">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                @if ($errors->any())
                                <div class="col-md-12 mb-4">
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        {!! implode('<br />', $errors->all('<span>:message</span>')) !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="broadcast_driver" class="form-label fs-6 mt-2 fw-semibold">
                                        {{ __('Company name') }}
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <input value="{{ $setting['company_name'] ?? config('app.name') }}" name="company_name" type="text" class="form-control" id="company_name" placeholder="@lang('Type company name')" aria-describedby="company_name" />
                                </div>
                            </div>
                            <!-- form row -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="company_logo" class="form-label fs-6 mt-2 fw-semibold">
                                        {{ __('Company logo') }}
                                    </label>
                                    <p class="fs-6 text-secondary fst-italic">{{ __('(Recommended size: 210 x 50 px)') }}</p>
                                </div>
                                <div class="col-md-9">
                                    <div class="img-holder">
                                        <img class="company-img" src="{{ isset($setting['company_logo']) ? asset($setting['company_logo']) : asset('assets/img/company/logo.png') }}" alt="">
                                        <div class="text-center left-50 img-holder-placeholder">
                                            {{ __('Change logo') }}
                                        </div>
                                    </div>
                                    <input class="d-none" name="company_logo" type="file" accept="image/*" />
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="company_icon" class="form-label fs-6 mt-2 fw-semibold">
                                        {{ __('Company icon') }}
                                    </label>
                                    <p class="fs-6 text-secondary fst-italic">{{ __('(Recommended size: 50 x 50 px)') }}</p>
                                </div>
                                <div class="col-md-9">
                                    <div class="img-holder">
                                        <img class="company-img" src="{{ isset($setting['company_icon']) ? asset($setting['company_icon']) : asset('assets/img/company/logo.png') }}" alt="">
                                        <div class="text-center left-50 img-holder-placeholder">
                                            {{ __('Change icon') }}
                                        </div>
                                    </div>
                                    <input class="d-none" name="company_icon" type="file" accept=".ico" />
                                </div>
                            </div>
                            <!-- timezone -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="timezone" class="form-label fs-6 mt-2 fw-semibold">
                                        {{ __('Timezone') }}
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <select name="timezone" id="timezon" class="selectpicker w-100 dropup" data-style="btn-default" data-live-search="true">
                                        <option value="" selected>{{ __('Choose your preferred timezone') }}</option>
                                        @foreach (timezone_identifiers_list() as $timezone)
                                        <option value="{{ $timezone }}" {{ $timezone == ($setting['timezone'] ?? config('app.timezone')) ? 'selected' : '' }}>
                                            @lang($timezone)
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- submit form -->
                            <button data-form="ajax-form" type="submit" class="btn btn-primary me-sm-3 mb-4">@lang('Update')</button>
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