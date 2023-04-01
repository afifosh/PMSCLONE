@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.header', ['title' => 'Delivery Settings'])

@section('content')
<h4 class="fw-semibold mb-4">@lang('Settings')</h4>
<div class="app-setting card">
    <div class="row g-0">
        @include('admin.pages.settings.inc.tabs')
        <!-- Settings List -->
        <div class="col settings-list">
            <div class="shadow-none border-0">
                @include('admin.pages.settings.inc.card-header', ['heading' => 'Email Setup'])
                <hr>
                <div class="setting pt-0 px-4">
                    @php
                    $deliveryServices = [
                        'amazon_ses' => 'Amazon SES',
                        'mailgun' => 'Mailgun',
                        'smtp' => 'SMTP',
                        'sendmail' => 'Sendmail',
                        'mailtrap' => 'Mailtrap',
                    ];
                    @endphp
                    <div class="setting-item">
                        <div class="row">
                            <!-- Supported mail services -->
                            <div class="col-md-6 mb-4">
                                <label for="provider" class="form-label fs-6 mb-2 fw-semibold">
                                    @lang('Supported mail services')
                                </label>
                                <select id="provider" data-tokens="{{ route('admin.setting.delivery.show') }}" data-attr="{{ $settings['provider'] ?? '' }}" class="selectpicker w-100" data-style="btn-default" data-live-search="true">
                                    @foreach($deliveryServices as $key => $service)
                                    <option value="{{ $key }}" data-tokens="{{ $key }}" {{ isset($settings['provider']) && $settings['provider'] === $key ? 'selected' : '' }}>
                                        {{ __($service) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- AMAZON SES -->
                        <x-admin.settings.delivery.delivery-form identifier="amazon_sesService" provider="amazon_ses" :$settings>
                            @include('admin.pages.settings.delivery.inc.amazon-ses',
                            ['settings' => isset($settings['provider']) && $settings['provider'] === 'amazon_ses' ? $settings : null]
                            )
                        </x-admin.settings.delivery.delivery-form>
                        <!-- MAILGUN -->
                        <x-admin.settings.delivery.delivery-form identifier="mailgunService" provider="mailgun" :$settings>
                            @include('admin.pages.settings.delivery.inc.mailgun',
                            ['settings' => isset($settings['provider']) && $settings['provider'] === 'mailgun' ? $settings : null]
                            )
                        </x-admin.settings.delivery.delivery-form>
                        <!-- SMTP -->
                        <x-admin.settings.delivery.delivery-form identifier="smtpService" provider="smtp" :$settings>
                            @include('admin.pages.settings.delivery.inc.smtp',
                            ['settings' => isset($settings['provider']) && $settings['provider'] === 'smtp' ? $settings : null]
                            )
                        </x-admin.settings.delivery.delivery-form>
                        <!-- SENDMAIL -->
                        <x-admin.settings.delivery.delivery-form identifier="sendmailService" provider="sendmail" :$settings>
                            @include('admin.pages.settings.delivery.inc.sendmail',
                            ['settings' => isset($settings['provider']) && $settings['provider'] === 'sendmail' ? $settings : null]
                            )
                        </x-admin.settings.delivery.delivery-form>
                        <!-- MAILTRAP -->
                        <x-admin.settings.delivery.delivery-form identifier="mailtrapService" provider="mailtrap" :$settings>
                            @include('admin.pages.settings.delivery.inc.mailtrap',
                            ['settings' => isset($settings['provider']) && $settings['provider'] === 'mailtrap' ? $settings : null]
                            )
                        </x-admin.settings.delivery.delivery-form>
                    </div>
                </div>
            </div>
            <div class="app-overlay"></div>
        </div>
        <!-- /Settings List -->
    </div>
</div>
@endsection