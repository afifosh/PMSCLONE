<!-- Settings Sidebar -->
@php
$urls = [
    'general' => 'admin.setting.general.index',
    'security' => 'admin.setting.security.index',
    'email' => 'admin.setting.delivery.index',
    'broadcast' => 'admin.setting.broadcast.index',
    'onlyoffice' => 'admin.setting.onlyoffice.index',    
];
$active = Request::route()->getName();
@endphp
<div class="col setting-sidebar border-end flex-grow-0" id="setting-sidebar">
    <div class="btn-compost-wrapper d-grid"></div>
    <div class="setting-filter py-2">
        <ul class="setting-list list-unstyled mb-4">
            <li class="{{ $active === $urls['general'] ? 'active' : '' }} d-flex justify-content-between py-3" data-target="general">
                <a href="{{ route($urls['general']) }}" class="d-flex flex-wrap align-items-center">
                    <!-- <i class="tf-icons ti ti-settings"></i> -->
                    <span class="align-middle ms-2">@lang('General')</span>
                </a>
            </li>
            <li class="{{ $active === $urls['security'] ? 'active' : '' }} d-flex justify-content-between py-3" data-target="security">
                <a href="{{ route($urls['security']) }}" class="d-flex flex-wrap align-items-center">
                    <!-- <i class="ti ti-lock"></i> -->
                    <span class="align-middle ms-2">@lang('Security')</span>
                </a>
            </li>
            <li class="{{ $active === $urls['broadcast'] ? 'active' : '' }} d-flex justify-content-between py-3" data-target="broadcast">
                <a href="{{ route($urls['broadcast']) }}" class="d-flex flex-wrap align-items-center">
                    <!-- <i class="ti ti-bell"></i> -->
                    <span class="align-middle ms-2">@lang('Broadcast Setup')</span>
                </a>
            </li>
            <li class="{{ $active === $urls['email'] ? 'active' : '' }} d-flex py-3" data-target="email">
                <a href="{{ route($urls['email']) }}" class="d-flex flex-wrap align-items-center">
                    <!-- <i class="ti ti-mail"></i> -->
                    <span class="align-middle ms-2">@lang('Email Setup')</span>
                </a>
            </li>
            <li class="{{ $active === $urls['onlyoffice'] ? 'active' : '' }} d-flex py-3" data-target="onlyoffice">
                <a href="{{ route($urls['onlyoffice']) }}" class="d-flex flex-wrap align-items-center">
                    <!-- <i class="ti ti-mail"></i> -->
                    <span class="align-middle ms-2">@lang('OnlyOffice Setup')</span>
                </a>
            </li>            
        </ul>
    </div>
</div>