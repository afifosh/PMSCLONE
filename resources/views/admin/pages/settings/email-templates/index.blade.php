@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.email-template-header', ['title' => 'Email Templates'])
@section('content')

<h4 class="fw-semibold mb-4">@lang('Settings')</h4>
<div class="app-setting card">
    <div class="row g-0">
        @include('admin.pages.settings.inc.tabs')
        <!-- Settings List -->
        <div class="col settings-list">
            <div class="shadow-none border-0">
            <div class="emails-list-header px-3 pt-lg-3 pt-3">
    <div class="d-flex justify-content-between align-items-center d-flex">
        <div class="d-flex align-items-center w-100">
            <i class="ti ti-menu-2 ti-sm cursor-pointer d-block d-lg-none me-3" data-bs-toggle="sidebar" data-target="#setting-sidebar" data-overlay></i>
            <h5 class="mb-0 px-2">Email Templates</h5>
            
        </div>
        <div class="form-actions items-center d-flex">
        <div class=" align-items-center sm:mr-3 d-flex">    
        <span class="mr-2 text-sm text-neutral-700 d-block">Locale:&nbsp;</span>
        <div class="dropdown">
                    <button
                        type="button"
                        class="btn dropdown-toggle p-2"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <span class="text-muted fw-normal me-2 d-none d-sm-inline-block">en</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="javascript:fetch('en');">en</a>
                        </li>
                        <!-- <li>
                            <a class="dropdown-item" href="javascript:fetch('es');">es</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:fetch('pt_BR');">pt_BR</a>
                        </li> -->
                    </ul>
                </div>
    
    </div>
        <div class=" align-items-center sm:mr-3 d-flex">    
        <span class="mr-2 text-sm text-neutral-700 d-flex">Template:&nbsp;</span>
        <div class="dropdown inline-flex">
                    <button
                        type="button"
                        class="btn btn-default dropdown-toggle p-2"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{$templates[0]['name']}}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                    @foreach ($templates as $template)
                        <li>
                            <a class="dropdown-item" href="javascript:setActive({{json_encode($template)}});">{{$template['name']}}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

                <hr>
                <div class="setting pt-0 px-4">
                    <div class="setting-item" data-general="true" data-bs-toggle="sidebar">
                        <!-- form -->
                        <form class="mt-3" id="email-templates-form">
                            @csrf
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
                                        {{ __('Subject') }}
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <input name="templateId" type="hidden" id="templateId"/>
                                    <input name="subject" type="text" class="form-control" id="subject" aria-describedby="subject" />
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="broadcast_driver" class="form-label fs-6 mt-2 fw-semibold">
                                        {{ __('Message') }}
                                    </label>
                                </div>
                                <div class="col-md-9">
                                <div class="">
              <div class="d-flex justify-content-end">
                <div class="email-editor-toolbar border-bottom-0 w-100">
                  <span class="ql-formats me-0">
                    <button class="ql-bold"></button>
                    <button class="ql-italic"></button>
                    <button class="ql-underline"></button>
                    <button class="ql-list" value="ordered"></button>
                    <button class="ql-list" value="bullet"></button>
                    <button class="ql-link"></button>
                    <button class="ql-image"></button>
                  </span>
                </div>
              </div>
              <div id="email-editor" class="email-editor"></div>
                                    <input type="hidden" name="html_template" id="html_template" />
                                </div>
                            </div></div>
                            <div class="row mb-4 mt-4">
                                <div class="col-md-3">
                                    <label for="broadcast_driver" class="form-label fs-6 mt-2 fw-semibold">
                                        {{ __('Placeholders') }}
                                    </label>
                                </div>
                                <div class="col-md-9"  >
                            <div class="form-control" id="placeholders" style="background:lightgrey">
                        
                        </div>
                            </div>
                            </div>
                            <!-- submit form -->
                            <a id="save-template" href="javascript:submit(this);" class="btn btn-primary me-sm-3 mb-4">@lang('Save')</a>
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
