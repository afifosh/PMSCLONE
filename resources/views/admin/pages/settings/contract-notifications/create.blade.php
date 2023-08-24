@extends('admin/layouts/layoutMaster')

@include('admin.pages.settings.inc.header', ['title' => 'Notifications Settings'])

@section('content')
<h4 class="fw-semibold mb-4">@lang('Settings')</h4>
<div class="app-setting card">
    <div class="row g-0">
        @include('admin.pages.settings.inc.tabs')

        <!-- Settings List -->
        <div class="col settings-list">
            <div class="shadow-none border-0">
                @include('admin.pages.settings.inc.card-header', ['heading' => 'Contract Notifications'])
                <hr>
                <div class="setting pt-0 px-4">
                    <div class="setting-item" data-bs-toggle="sidebar">
                        <!-- form -->
                        @php
                            $optionParameters = collect($admins)->mapWithKeys(function ($item) {
                                return [$item['id'] => ['data-full_name' => $item['full_name'], 'data-avatar' => $item['avatar']]];
                            })->all();
                            $selectedEmails = isset($setting['emails']) ? explode(',', $setting['emails']) : [];
                        @endphp
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.setting.contract-notifications.update') }}" class="mt-3" id="general-setting-form">
                          @method('PUT')
                          <div class="mb-3">
                            <label for="" class="form-label fs-6 mb-2 fw-semibold">Users to receive notifications</label>
                            {!! Form::select('emails[]', $admins->pluck('email', 'id'), $selectedEmails , ['class' => 'form-control select2User', 'multiple'], $optionParameters) !!}
                          </div>

                          <div class="mb-3">
                            <label for="" class="form-label fs-6 mb-2 fw-semibold">Schedule</label>
                            <div class="d-flex">
                              <div>
                                {!! Form::number('cycle_unit_value', @$setting['cycle_unit_value'], ['class' => 'form-control']) !!}
                              </div>
                              <div>
                                {!! Form::select('cycle_unit_name', ['Days' => 'Day(s)', 'Weeks' => 'Week(s)', 'Months' => 'Month(s)', 'Years' => 'Year(s)'], @$setting['cycle_unit_name'] , ['class' => 'form-control select2']) !!}
                              </div>
                            </div>
                          </div>

                          <div class="mb-3">
                            <label for="" class="form-label fs-6 mb-2 fw-semibold">Cycle</label>
                            <div class="d-flex">
                              <div>
                                {!! Form::number('cycle_count', @$setting['cycle_count'] ?? 0, ['class' => 'form-control']) !!}
                              </div>
                            </div>
                          </div>
                          <div class="form-group col-6">
                            <label class="switch d-flex flex-column">
                              {{ Form::label('enable_notifications', __('Enable Notifications'), ['class' => 'col-form-label fw-semibold']) }}
                              {{ Form::checkbox('enable_notifications', 1, @$setting['enable_notifications'],['class' => 'switch-input is-invalid'])}}
                              <span class="switch-toggle-slider position-relative mb-1">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                              </span>
                              <span class="switch-label"></span>
                            </label>
                          </div>

                            <!-- submit form -->
                            <button data-form="ajax-form" type="submit" class="btn btn-primary mt-4 me-sm-3 mb-4">@lang('Update')</button>
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
