@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.header', ['title' => 'Security Settings'])

@section('content')
<h4 class="fw-semibold mb-4">@lang('Settings')</h4>
<div class="app-setting card">
    <div class="row g-0">
        @include('admin.pages.settings.inc.tabs')

        <!-- Settings List -->
        <div class="col settings-list">
            <div class="shadow-none border-0">
                @include('admin.pages.settings.inc.card-header', ['heading' => 'Security'])
                <hr>
                <div class="setting pt-0 px-4">
                    <div class="setting-item" data-general="true" data-bs-toggle="sidebar">
                        <form method="POST" action="{{ route('admin.setting.security.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="type" value="security" />
                            <div class="row">
                                <!-- Password expires after -->
                                <div class="col-md-6 mb-3">
                                    <label for="passwordExpiryDays" class="form-label fs-6 mb-2 fw-semibold @error('password_expiry_days') is-invalid @enderror">
                                        @lang('Password expires after')
                                    </label>
                                    <input name="password_expire_days" value="{{ $setting['password_expire_days'] ?? config('auth.password_expire_days') }}" type="text" class="form-control" id="passwordExpiryDays" placeholder="{{ __('Number of days') }}" aria-describedby="passwordExpiryDays" />
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

                                <!-- Password history depth -->
                                <div class="col-md-6 mb-3">
                                    <label for="passwordHistoryDepth" class="form-label fs-6 mb-2 fw-semibold @error('password_expiry_days') is-invalid @enderror">
                                        @lang('Password history depth')
                                    </label>
                                    <input name="password_history_depth" value="{{ $setting['password_history_depth'] ?? config('auth.password_history_depth') }}" type="text" class="form-control" id="passwordHistoryDepth" placeholder="{{ __('Password history depth') }}" aria-describedby="passwordHistoryDepth" />
                                    @error('password_history_depth')
                                    <div class="alert alert-danger alert-dismissible my-2">
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <div id="defaultFormControlHelp" class="form-text">
                                        @lang('Please input the number of password history depth')
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
                                    <input name="timeout_warning_seconds" value="{{ $setting['timeout_warning_seconds'] ?? config('auth.timeout_warning_seconds') }}" type="text" class="form-control" id="timeoutWarningIn" placeholder="{{ __('Timeout warning after x seconds') }}" aria-describedby="timeoutWarningIn" />
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
                                    <input name="timeout_after_seconds" value="{{ $setting['timeout_after_seconds'] ?? config('auth.timeout_after_seconds') }}" type="text" class="form-control" id="timeoutAfter" placeholder="{{ __('Timeout after x amount of seconds') }}" aria-describedby="timeoutAfter" />
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
                            <button data-form="ajax-form" type="submit" class="btn btn-primary me-sm-3">@lang('Update')</button>
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