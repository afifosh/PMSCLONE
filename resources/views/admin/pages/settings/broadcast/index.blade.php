@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.header', ['title' => 'Broadcast Setup'])

@section('content')
<h4 class="fw-semibold mb-4">@lang('Settings')</h4>
<div class="app-setting card">
    <div class="row g-0">
        @include('admin.pages.settings.inc.tabs')

        <!-- Settings List -->
        <div class="col settings-list">
            <div class="shadow-none border-0">
                @include('admin.pages.settings.inc.card-header', ['heading' => 'Broadcast Setup'])
                <hr>
                <div class="setting pt-0 px-4">
                    <div class="setting-item" data-general="true" data-bs-toggle="sidebar">
                        <h5>{{__('Please restart queue worker and socket server after updating these settings.')}}</h5>
                        <!-- form -->
                        <form method="POST" action="{{ route('admin.setting.broadcast.update') }}" class="mt-3">
                            @csrf
                            @method('PUT')
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="broadcast_driver" class="form-label fs-6 mt-2 fw-semibold">
                                        {{ __('Broadcast Driver') }}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <select onchange="updateSocketInputs();" name="broadcast_driver" id="broadcast_driver" data-attr="{{ $setting['broadcast_driver'] ?? '' }}" class="form-select"{{--selectpicker--}} data-style="btn-default" data-live-search="true">
                                        <option value="pusher" data-tokens="pusher" {{ isset($setting['broadcast_driver']) && $setting['broadcast_driver'] === 'pusher' ? 'selected' : '' }}>
                                            {{ __('Pusher') }}
                                        </option>
                                        <option value="websockets" data-tokens="websockets" {{ isset($setting['broadcast_driver']) && $setting['broadcast_driver'] === 'websockets' ? 'selected' : '' }}>
                                          {{ __('Laravel WebSockets') }}
                                      </option>
                                    </select>
                                </div>
                            </div>
                            <!-- pusher app id -->
                            <div class="row mb-4">
                                <!-- Pusher App id -->
                                <div class="col-md-3">
                                    <label for="pusherAppId" class="mt-2 form-label fs-6 fw-semibold @error('pusher_app_id') is-invalid @enderror">
                                        @lang('Pusher App Id')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="pusher_app_id" value="{{ $setting['pusher_app_id'] ?? config('broadcasting.connections.pusher.app_id') }}" type="text" class="form-control" id="pusherAppId" placeholder="{{ __('Pusher App id') }}" aria-describedby="pusherAppId" />
                                </div>
                            </div>
                            <!-- pusher app key -->
                            <div class="row mb-4">
                                <!-- Pusher App Key -->
                                <div class="col-md-3">
                                    <label for="pusherAppKey" class="mt-2 form-label fs-6 fw-semibold @error('pusher_app_key') is-invalid @enderror">
                                        @lang('Pusher App Key')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="pusher_app_key" value="{{ $setting['pusher_app_key'] ?? config('broadcasting.connections.pusher.key') }}" type="text" class="form-control" id="pusherAppKey" placeholder="{{ __('Pusher App Key') }}" aria-describedby="pusherAppKey" />
                                </div>
                            </div>
                            <!-- pusher app secret -->
                            <div class="row mb-4">
                                <!-- Pusher App Secret -->
                                <div class="col-md-3">
                                    <label for="pusherAppSecret" class="mt-2 form-label fs-6 fw-semibold @error('pusher_app_secret') is-invalid @enderror">
                                        @lang('Pusher App Secret')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="pusher_app_secret" value="{{ $setting['pusher_app_secret'] ?? config('broadcasting.connections.pusher.secret') }}" type="text" class="form-control" id="pusherAppSecret" placeholder="{{ __('Pusher App Secret') }}" aria-describedby="pusherAppSecret" />
                                </div>
                            </div>
                            <!-- pusher app cluster -->
                            <div class="row mb-4">
                                <!-- Pusher App Cluster -->
                                <div class="col-md-3">
                                    <label for="pusherAppCluster" class="mt-2 form-label fs-6 fw-semibold @error('pusher_app_cluster') is-invalid @enderror">
                                        @lang('Pusher App Cluster')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="pusher_app_cluster" value="{{ $setting['pusher_app_cluster'] ?? config('broadcasting.connections.pusher.options.cluster') }}" type="text" class="form-control" id="pusherAppCluster" placeholder="{{ __('Pusher App Cluster') }}" aria-describedby="pusherAppCluster" />
                                </div>
                            </div>
                            <div class="websocket-inputs" style="display:{{ @$setting['broadcast_driver'] == 'websockets' ? '' : 'none' }}">
                              <div class="row mb-4">
                                <!-- Pusher App Cluster -->
                                <div class="col-md-3">
                                    <label class="mt-2 form-label fs-6 fw-semibold @error('app_host') is-invalid @enderror">
                                        @lang('App Host')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="app_host" value="{{ $setting['app_host'] ?? config('broadcasting.connections.pusher.options.host') }}" type="text" class="form-control" placeholder="{{ __('App Host') }}"/>
                                </div>
                              </div>
                              <div class="row mb-4">
                                <!-- Pusher App Cluster -->
                                <div class="col-md-3">
                                    <label class="mt-2 form-label fs-6 fw-semibold @error('app_port') is-invalid @enderror">
                                        @lang('App Port')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="app_port" value="{{ $setting['app_port'] ?? config('broadcasting.connections.pusher.options.port') }}" type="text" class="form-control" placeholder="{{ __('App Port') }}"/>
                                </div>
                              </div>
                              <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="broadcast_driver" class="form-label fs-6 mt-2 fw-semibold">
                                        {{ __('Broadcast Driver') }}
                                    </label>
                                </div>
                                <div class="col-md-8">
                                  <select name="app_scheme" class="form-select">
                                    <option value="https" @selected(@$setting['app_scheme'] == 'https')>https</option>
                                    <option value="http" @selected(@$setting['app_scheme'] == 'http')>http</option>
                                </select>
                                </div>
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
<script>
    function updateSocketInputs() {
      if ($('#broadcast_driver').val() == 'websockets') {
        $('.websocket-inputs').show();
      } else {
        $('.websocket-inputs').hide();
      }
    }
</script>
@endsection
