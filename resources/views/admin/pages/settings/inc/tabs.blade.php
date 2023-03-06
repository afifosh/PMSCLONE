<!-- Settings Sidebar -->
@php
    $urls = [
        'general' => 'admin.setting.index',
        'email' => 'admin.setting.email',
    ];
    $active = Request::route()->getName();
@endphp
<div class="col setting-sidebar border-end flex-grow-0" id="setting-sidebar">
    <div class="btn-compost-wrapper d-grid"></div>
    <div class="setting-filter py-2">
        <ul class="setting-list list-unstyled mb-4">
            <li class="{{ $active === $urls['general'] ? 'active' : '' }} d-flex justify-content-between py-3" data-target="general">
                <a href="{{ route($urls['general']) }}" class="d-flex flex-wrap align-items-center">
                    <i class="ti ti-mail"></i>
                    <span class="align-middle ms-2">@lang('General')</span>
                </a>
            </li>
            <li class="{{ $active === $urls['email'] ? 'active' : '' }} d-flex py-3" data-target="email">
                <a href="{{ route($urls['email']) }}" class="d-flex flex-wrap align-items-center">
                    <i class="ti ti-mail"></i>
                    <span class="align-middle ms-2">@lang('Email Setup')</span>
                </a>
            </li>
        </ul>
    </div>
</div>