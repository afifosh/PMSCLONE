@extends('admin.layouts/layoutMaster')
@section('title', 'Email Templates')
@section('vendor-style')
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
  <style>
    .email_temp {
      max-height: 450px !important;
      overflow-y: scroll;
    }
    .note-group-select-from-files {
      display: none;
    }
  </style>
@endsection
@section('vendor-script')
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
@endsection
@section('page-script')
<script>
  $(document).ready(function () {
    $('.summernote-simple').summernote();
        var styleEle = $("style#fixed");
      $("<style id=\"fixed\">.note-editor .dropdown-toggle::after { all: unset; } .note-editor .note-dropdown-menu { box-sizing: content-box; } .note-editor .note-modal-footer { box-sizing: content-box; }</style>")
      .prependTo("body");
  });
</script>
@endsection


@section('content')
<div class="row">
  <div class="col-lg-6">
      <div class="d-flex align-items-center">
              <h5 class="mb-0 text-dark">{{__('Email Templates')}}</h5>
      </div>
  </div>
  <div class="col-lg-6">
      <div class="text-end">
          <div class="d-flex justify-content-end drp-languages">
              <ul class="list-unstyled p-2 m-2">
                  <li class="dropdown dash-h-item drp-language">
                      <a class="email-color dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                         href="#" role="button" aria-haspopup="false" aria-expanded="false"
                         id="dropdownLanguage">
                          <span
                              class="email-color drp-text hide-mob text-primary me-2">{{ ucfirst($LangName) }}</span>
                      </a>
                      <div class="dropdown-menu dash-h-dropdown dropdown-menu-end email_temp"
                           aria-labelledby="dropdownLanguage">
                          @foreach ($languages as $code => $lang)
                              <a href="{{ route('admin.manage.email.language', [$emailTemplate->id, $code]) }}"
                                 class="dropdown-item {{ $currEmailTempLang->lang == $code ? 'text-primary' : '' }}">{{ ucfirst($lang) }}</a>
                          @endforeach
                      </div>
                  </li>
              </ul>
              <ul class="list-unstyled p-2 m-2">
                  <li class="dropdown dash-h-item drp-language">
                      <a class="email-color dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                         href="#" role="button" aria-haspopup="false" aria-expanded="false"
                         id="dropdownLanguage">
                          <span class="drp-text hide-mob text-primary">{{ __('Template: ') }}{{ $emailTemplate->name }}</span>
                      </a>
                      <div class="dropdown-menu dash-h-dropdown dropdown-menu-end email_temp" aria-labelledby="dropdownLanguage">
                          @foreach ($EmailTemplates as $EmailTemplate)
                              <a href="{{ route('admin.manage.email.language', [$EmailTemplate->id,(Request::segment(4)?Request::segment(4):\Auth::user()->lang)]) }}"
                                 class="dropdown-item {{$EmailTemplate->name == $emailTemplate->name ? 'text-primary' : '' }}">{{ $EmailTemplate->name }}
                              </a>
                          @endforeach
                      </div>
                  </li>
              </ul>
          </div>
      </div>
  </div>
</div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body ">
                    {{Form::model($currEmailTempLang, array('route' => array('admin.email_template.update', $id), 'method' => 'PUT')) }}
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h6 class="font-weight-bold pb-1">{{__('Placeholders')}}</h6>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row text-xs">
                                      <div class="row">
                                        <p class="col-4">{{__('App Name')}} : <span class="pull-end text-primary">{app_name}</span></p>
                                        <p class="col-4">{{__('App Url')}} : <span class="pull-right text-primary">{app_url}</span></p>
                                        <p class="col-4">{{__('User Name')}} : <span class="pull-right text-primary">{user_name}</span></p>
                                        <p class="col-4">{{__('User Email')}} : <span class="pull-right text-primary">{user_email}</span></p>
                                        @if($emailTemplate->slug=='new_user')
                                          <p class="col-4">{{__('Password')}} : <span class="pull-right text-primary">{password}</span></p>
                                        @elseif($emailTemplate->slug=='two_factor_code')
                                          <p class="col-4">{{__('Two Factor Code')}} : <span class="pull-end text-primary">{two_factor_code}</span></p>
                                        @elseif($emailTemplate->slug=='verify_email')
                                          <p class="col-4">{{__('Verification URL')}} : <span class="pull-end text-primary">{verification_url}</span></p>
                                        @elseif($emailTemplate->slug=='reset_password')
                                          <p class="col-4">{{__('Password Reset link')}} : <span class="pull-end text-primary">{password_reset_link}</span></p>
                                          <p class="col-4">{{__('Link Expiry Time')}} : <span class="pull-end text-primary">{link_expiry_time}</span></p>
                                        @elseif($emailTemplate->slug=='contract_task_reminder')
                                          <p class="col-4">{{__('Task Subject')}} : <span class="pull-end text-primary">{task_subject}</span></p>
                                          <p class="col-4">{{__('Set By Name')}} : <span class="pull-end text-primary">{reminder_set_by_name}</span></p>
                                          <p class="col-4">{{__('Set By Email')}} : <span class="pull-end text-primary">{reminder_set_by_email}</span></p>
                                          <p class="col-4">{{__('Task View URL')}} : <span class="pull-end text-primary">{task_view_url}</span></p>
                                          <p class="col-4">{{__('Reminder Desc')}} : <span class="pull-end text-primary">{reminder_description}</span></p>
                                        @elseif($emailTemplate->slug=='contract_expiry')
                                          <p class="col-4">{{__('Subject')}} : <span class="pull-end text-primary">{contract_subject}</span></p>
                                          <p class="col-4">{{__('End Date')}} : <span class="pull-end text-primary">{contract_end_date}</span></p>
                                          <p class="col-4">{{__('View URL')}} : <span class="pull-end text-primary">{contract_view_url}</span></p>
                                        @elseif($emailTemplate->slug=='contract_termination')
                                          <p class="col-4">{{__('Subject')}} : <span class="pull-end text-primary">{contract_subject}</span></p>
                                          <p class="col-4">{{__('View URL')}} : <span class="pull-end text-primary">{contract_view_url}</span></p>
                                        @elseif(in_array($emailTemplate->slug, ['new_location_login', 'new_device_login', 'failed_login']))
                                          <p class="col-4">{{__('Time')}} : <span class="pull-end text-primary">{time}</span></p>
                                          <p class="col-4">{{__('IP Address')}} : <span class="pull-end text-primary">{ip_address}</span></p>
                                          <p class="col-4">{{__('browser')}} : <span class="pull-end text-primary">{browser}</span></p>
                                          <p class="col-4">{{__('Location City')}} : <span class="pull-end text-primary">{location_city}</span></p>
                                          <p class="col-4">{{__('Location State')}} : <span class="pull-end text-primary">{location_state}</span></p>
                                        @endif
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('subject', null, ['class' => 'form-control font-style', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('from', __('From'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('from', $emailTemplate->from, ['class' => 'form-control font-style', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-12">
                            {{ Form::label('content', __('Email Message'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::textarea('content', $currEmailTempLang->content, ['class' => 'summernote-simple', 'required' => 'required']) }}
                        </div>
                        <div class="modal-footer">
                            {{ Form::hidden('lang', $template_lang) }}
                            <button class="btn btn-primary">{{__('Save Changes')}}</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

