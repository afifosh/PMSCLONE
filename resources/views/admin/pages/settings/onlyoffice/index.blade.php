@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.header', ['title' => 'Only Office Setup'])

@section('content')
<h4 class="fw-semibold mb-4">@lang('Settings')</h4>
<div class="app-setting card">
    <div class="row g-0">
        @include('admin.pages.settings.inc.tabs')

        <!-- Settings List -->
        <div class="col settings-list">
            <div class="shadow-none border-0">
                @include('admin.pages.settings.inc.card-header', ['heading' => 'OnlyOffice Setup'])
                <hr>
                <div class="setting pt-0 px-4">
                    <div class="setting-item" data-general="true" data-bs-toggle="sidebar">
                        <!-- form -->
                        <form method="POST" action="{{ route('admin.setting.onlyoffice.update') }}" class="mt-3">
                            @csrf
                            @method('PUT')
                            <div class="row mb-4">

                                <!-- Server Setting -->
                                <div class="col-md-12">
                                    <label class="mt-2 form-label fs-6 fw-semibold">
                                        @lang('Server Setting')
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <p class="settings-hint">ONLYOFFICE Docs Location specifies the address of the server with the document services installed. Please change the, "{{ __('<documentserver>') }}" for the server address in the below line.</p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <!-- ONLYOFFICE Docs address -->
                                <div class="col-md-3">
                                    <label for="docServerURL" class="mt-2 form-label fs-6 fw-semibold @error('doc_server_url') is-invalid @enderror">
                                        @lang('ONLYOFFICE Docs address')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="doc_server_url" value="{{ $setting['doc_server_url'] ?? config('onlyoffice.doc_server_url') }}" type="text" class="form-control" id="docServerURL" placeholder="{{ __('https://<documentserver>/') }}" aria-describedby="docServerURL" />
                                </div>
                            </div>

                            <div class="row mb-4">
                                <!-- ONLYOFFICE Docs address API -->
                                <div class="col-md-3">
                                    <label for="docServerAPIURL" class="mt-2 form-label fs-6 fw-semibold @error('doc_server_api_url') is-invalid @enderror">
                                        @lang('ONLYOFFICE Docs address API')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="doc_server_api_url" value="{{ $setting['doc_server_api_url'] ?? config('onlyoffice.doc_server_api_url') }}" type="text" class="form-control" id="docServerAPIURL" placeholder="{{ __('https://<documentserver>/web-apps/apps/api/documents/api.js') }}" aria-describedby="docServerAPIURL" />
                                </div>
                            </div>

                            <div class="row mb-4">
                                <!-- Secret key (leave blank to disable) -->
                                <div class="col-md-3">
                                    <label for="docSecretKey" class="mt-2 form-label fs-6 fw-semibold @error('secret') is-invalid @enderror">
                                        @lang('Secret key (leave blank to disable)')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="secret" value="{{ $setting['secret'] ?? config('onlyoffice.secret') }}" type="text" class="form-control" id="docSecretKey" placeholder="{{ __('secret key') }}" aria-describedby="docSecretKey" />
                                </div>
                            </div>


                            <div class="row mb-4">
                                <!-- Max File size for upload  -->
                                <div class="col-md-3">
                                    <label for="docMaxFileSizeAllowed" class="mt-2 form-label fs-6 fw-semibold @error('allowed_file_size') is-invalid @enderror">
                                        @lang('Max File size for upload')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="allowed_file_size" value="{{ $setting['allowed_file_size'] ?? config('onlyoffice.allowed_file_size') }}" type="text" class="form-control" id="docMaxFileSizeAllowed" placeholder="{{ __('32') }}" aria-describedby="docMaxFileSizeAllowed" />
                                </div>
                            </div>


                            {{-- <div class="row mb-4">
                                <!-- Allowed file types for upload -->
                                <div class="col-md-3">
                                    <label for="docAllowedFileTypes" class="mt-2 form-label fs-6 fw-semibold @error('supported_files') is-invalid @enderror">
                                        @lang('Allowed file types for upload')
                                    </label>
                                </div>
                                <div class="col-md-8">
                                    <input name="supported_files" value="{{ $setting['supported_files'] ?? '' }}" type="text" class="form-control" id="docAllowedFileTypes" placeholder="{{ __('secret key') }}" aria-describedby="docAllowedFileTypes" />
                                    <textarea type="text" name="allowed_file_types" id="allowed_file_types"
                                    placeholder="e.g. application/x-zip-compressed"
                                    class="form-control f-14">{{ $setting['supported_files'] ?? '' }}</textarea>
                                </div>
                            </div> --}}


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
