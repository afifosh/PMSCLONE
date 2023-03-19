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
                        
                    </div>
                </div>
            </div>
            <div class="app-overlay"></div>
        </div>
        <!-- /Settings List -->
    </div>
</div>
@endsection