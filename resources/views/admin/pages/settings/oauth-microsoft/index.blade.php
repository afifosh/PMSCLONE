@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.header', ['title' => 'Microsoft Oauth Setup'])

@section('content')
<h4 class="fw-semibold mb-4">@lang('Settings')</h4>
<div class="app-setting card">
    <div class="row g-0">
        @include('admin.pages.settings.inc.tabs')
        <!-- Settings List -->
        <div class="col settings-list">
            <div class="shadow-none border-0">
                @include('admin.pages.settings.inc.card-header', ['heading' => 'Microsoft Oauth Setup'])
                <hr>
                <div class="setting pt-0 px-4">
                    <div class="setting-item" data-general="true" data-bs-toggle="sidebar">
                        <!-- form -->
                        <form method="POST" action="{{ route('admin.setting.oauth-microsoft.store') }}" class="mt-3">
                            @csrf
                            <div class="row">
                              <div class="mb-3">
                                <label class="form-label fw-semibold">@lang('Redirect Url')</label>
                                <input type="text" class="form-control" value="{{url('/').config('core.microsoft.redirect_uri')}}" disabled>
                              </div>
                            </div>
                            <div class="row">
                              <!-- client id -->
                              <div class="col-6 mb-3">
                                <label for="microsoft_client_id" class="form-label fw-semibold">@lang('Client Id')</label>
                                <input type="text" class="form-control" name="microsoft_client_id" id="microsoft_client_id" value="{{ $setting['microsoft_client_id'] ?? '' }}">
                              </div>
                              <!-- client secret -->
                              <div class="col-6 mb-3">
                                <label for="microsoft_client_secret" class="form-label fw-semibold">@lang('Client Secret')</label>
                                <input type="password" class="form-control" name="microsoft_client_secret" id="microsoft_client_secret" value="{{ $setting['microsoft_client_secret'] ?? '' }}">
                              </div>
                            </div>
                            <!-- button to submit form -->
                            <button data-form="ajax-form" type="submit" class="btn btn-primary me-sm-3">@lang('Update')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
