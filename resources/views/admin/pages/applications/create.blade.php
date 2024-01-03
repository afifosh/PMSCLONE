@php
  $configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Applications')
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{$application->id ? 'Edit' : 'Create'}} Application</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('admin.dashboard'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item">{!! Html::link(route('admin.applications.index'), __('Applications'), []) !!}</li>
            <li class="breadcrumb-item active">{{$application->id ? 'Edit' : 'Create'}} Application</li>
        </ul>
    </div>
@endsection

@section('content')
  @if ($application->id)
    {!! Form::model($application, ['route' => ['admin.applications.update', ['application' => $application]],
        'method' => 'PUT'
    ]) !!}
  @else
    {!! Form::model($application, ['route' => ['admin.applications.store'], 'method' => 'POST']) !!}
  @endif

  <!-- Vertical Wizard -->
  <div class="col-12 mb-4">
    <small class="text-light fw-medium">Application</small>
    <div class="bs-stepper wizard-vertical vertical mt-2">
      <div class="bs-stepper-header">
        <div class="step" data-target="#application-details">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title">Application Details</span>
              <span class="bs-stepper-subtitle">Setup Application Details</span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#form-design-info">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">2</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title">Form Info</span>
              <span class="bs-stepper-subtitle">Add Form Info</span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#application-team">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">3</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title">Application Team</span>
              <span class="bs-stepper-subtitle">Manage Application Team</span>
            </span>
          </button>
        </div>
        <div class="line"></div>
        <div class="step" data-target="#application-submitters">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">4</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title">Application Submitters</span>
              <span class="bs-stepper-subtitle">Manage Application Submitters</span>
            </span>
          </button>
        </div>
      </div>
      <div class="bs-stepper-content">
          <!-- Application Details -->
          <div id="application-details" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Application Details</h6>
              <small>Please Fill Application Details.</small>
            </div>
            <hr>
            <div class="row g-3">
              {{-- name --}}
                <div class="form-group col-6">
                  {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
                  {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
              </div>

              {{-- select program --}}
              <div class="form-group col-6">
                {{ Form::label('program_id', __('Program'), ['class' => 'col-form-label']) }}
                {!! Form::select('program_id', $programs ?? [], $selectedProgram ?? null, [
                'data-placeholder' => __('Select Program'),
                'class' => 'form-select select2Remote',
                'data-url' => route('resource-select', ['Program']),
                'data-allow-clear' => 'true'
                ])!!}
              </div>

              {{-- select type --}}
              <div class="form-group col-6">
                {{ Form::label('type_id', __('Type'), ['class' => 'col-form-label']) }}
                {!! Form::select('type_id', $types ?? [], $selectedType ?? null, [
                'data-placeholder' => __('Select Type'),
                'class' => 'form-select select2Remote',
                'data-url' => route('resource-select', ['ApplicationType']),
                'data-allow-clear' => 'true'
                ])!!}
              </div>

              {{-- select category --}}
              <div class="form-group col-6">
                {{ Form::label('category_id', __('Category'), ['class' => 'col-form-label']) }}
                {!! Form::select('category_id', $categories ?? [], $selectedCategory ?? null, [
                'data-placeholder' => __('Select Category'),
                'class' => 'form-select select2Remote',
                'data-url' => route('resource-select', ['ApplicationCategory']),
                'data-allow-clear' => 'true'
                ])!!}
              </div>

              {{-- select pipeline --}}
              <div class="form-group col-6">
                {{ Form::label('pipeline_id', __('Pipeline'), ['class' => 'col-form-label']) }}
                {!! Form::select('pipeline_id', $pipelines ?? [], $selectedPipeline ?? null, [
                'data-placeholder' => __('Select Pipeline'),
                'class' => 'form-select select2Remote',
                'data-url' => route('resource-select', ['ApplicationPipeline']),
                'data-allow-clear' => 'true'
                ])!!}
              </div>

              {{-- select score_card --}}
              <div class="form-group col-6">
                {{ Form::label('score_card_id', __('Score Card'), ['class' => 'col-form-label']) }}
                {!! Form::select('scorecard_id', $scoreCards ?? [], $selectedScoreCard ?? null, [
                'data-placeholder' => __('Select Score Card'),
                'class' => 'form-select select2Remote',
                'data-url' => route('resource-select', ['ApplicationScoreCard']),
                'data-allow-clear' => 'true'
                ])!!}
              </div>
              {{-- start_at --}}
              <div class="form-group col-6 mt-1">
                {{ Form::label('start_at', __('Start At'), ['class' => 'col-form-label']) }}
                {!! Form::text('start_at', null, ['class' => 'form-control flatpickr', 'placeholder' => __('Start At')]) !!}
              </div>

              {{-- <div class="mt-2 row">
                <div class="col-lg-12">
                    <div class="form-group">
                        {{ Form::label('set_end_date', __('Set end date'), ['class' => 'form-label']) }}
                        <label class="mt-2 form-switch float-end custom-switch-v1">
                            <input type="hidden" name="set_end_date" value="0">
                            <input type="checkbox" name="set_end_date" id="m_set_end_date"
                                class="form-check-input input-primary" {{ 'unchecked' }} value="1">
                        </label>
                    </div>
                </div>
              </div>

              <div id="set_end_date" class="{{ 'd-none' }}">
                  <div class="form-group">
                      <input class="form-control" name="set_end_date_time" id="set_end_date_time">
                  </div>
              </div> --}}
              {{-- has End date --}}
              <div class="col-6 mt-4 mb-2">
                <label class="switch mt-3">
                  {{ Form::checkbox('has_end_date', 1, $application->end_at && 1,['class' => 'switch-input'])}}
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                  <span class="switch-label">Has End Date?</span>
                </label>
              </div>
              {{-- end_at --}}
              <div class="form-group col-6 {{$application->end_at ?: 'd-none'}}">
                  {{ Form::label('end_at', __('End At'), ['class' => 'col-form-label']) }}
                  {!! Form::text('end_at', null, ['class' => 'form-control flatpickr', 'placeholder' => __('End At')]) !!}
              </div>
              {{-- description --}}
              <div class="form-group col-12">
                  {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
                  {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5, 'placeholder' => __('Description')]) !!}
              </div>
              <div class="col-12 d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button type="button" class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Form Info -->
          <div id="form-design-info" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Form Info</h6>
              <small>Enter Form Info.</small>
            </div>
            <hr>
            <div class="row g-3">
              {{-- select Form --}}
              <div class="form-group col-12">
                {{ Form::label('form_id', __('Form'), ['class' => 'col-form-label']) }}
                {!! Form::select('form_id', $forms ?? [], $selectedForm ?? null, [
                'data-placeholder' => __('Select Form'),
                'class' => 'form-select select2Remote',
                'data-url' => route('resource-select', ['Form']),
                'data-allow-clear' => 'true'
                ])!!}
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                    {{ Form::label('success_msg', __('Success Message'), ['class' => 'form-label']) }}
                    {!! Form::textarea('success_msg', null, [
                        'rows' => 5,
                        'id' => 'success_msg',
                        'placeholder' => __('Enter success message'),
                        'class' => 'form-control',
                    ]) !!}
                    @if ($errors->has('success_msg'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('success_msg') }}</strong>
                        </span>
                    @endif
                </div>
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                    {{ Form::label('notification_emails[]', __('Recipient Email'), ['class' => 'form-label']) }}
                    {!! Form::text('notification_emails[]', null, [
                        'class' => 'form-control',
                        'placeholder' => __('Enter recipient email'),
                    ]) !!}
                </div>
              </div>
              <div class="col-lg-12">
                  <div class="form-group">
                      {{ Form::label('notification_emails_cc[]', __('Cc Emails (Optional)'), ['class' => 'form-label']) }}
                      {!! Form::text('notification_emails_cc[]', null, [
                          'class' => 'form-control inputtags',
                          'placeholder' => __('Enter recipient cc email'),
                      ]) !!}
                  </div>
              </div>
              <div class="col-lg-12">
                  <div class="form-group">
                      {{ Form::label('notification_emails_bcc[]', __('Bcc Emails (Optional)'), ['class' => 'form-label']) }}
                      {!! Form::text('notification_emails_bcc[]', null, [
                          'class' => 'form-control inputtags',
                          'placeholder' => __('Enter recipient bcc email'),
                      ]) !!}
                  </div>
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                    {{ Form::label('allow_comments', __('Allow comments'), ['class' => 'form-label']) }}
                    <label class="mt-2 form-switch float-end custom-switch-v1">
                        <input type="checkbox" name="allow_comments" id="allow_comments"
                            class="form-check-input input-primary" {{ 'unchecked' }}>
                    </label>
                </div>
              </div>
              <div class="col-lg-12">
                  <div class="form-group">
                      {{ Form::label('allow_share_section', __('Allow Share Section'), ['class' => 'form-label']) }}
                      <label class="mt-2 form-switch float-end custom-switch-v1">
                          <input type="checkbox" name="allow_share_section" id="allow_share_section"
                              class="form-check-input input-primary" {{ 'unchecked' }}>
                      </label>
                  </div>
              </div>

              <div class="col-12 d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                  <span class="align-middle d-sm-inline-block d-none">Previous</span>
                </button>
                <button type="button" class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
              </div>
            </div>
          </div>
          <!-- Application Team -->
          <div id="application-team" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Application Team</h6>
              <small>Manage Application Team.</small>
            </div>
            <hr>
            {{--  --}}
            <div class="form-group col-12 mb-2">
              {{ Form::label('select_user', __('Users'), ['class' => 'col-form-label']) }}
              {!! Form::select('select_user', $users ?? [], $selectedUsers ?? null, [
              'id' => 'select2Basic',
              'data-placeholder' => __('Select Users'),
              'class' => 'form-select select2UserRemote',
              'data-url' => route('resource-select-user', ['Admin']),
              'onchange' => 'addUser(this);',
              'data-allow-clear' => 'true'
              ])!!}
            </div>

            <!-- <h4 class="mb-4 pb-2">1 Member</h4> -->
            <ul id="members-list" class="p-0 m-0">
              @forelse($application->users as $user)
                <li class="d-flex flex-wrap mb-3" data-user-id="{{$user->id}}">
                  <span class="mx-3 pt-2 cursor-pointer" onclick="$(this).parents('li').remove();"><i class="mr-0 fa fa-xl fa-xmark"></i></span>
                  <input type="hidden" name="application_users[]" value="{{$user->id}}">
                  <div class="avatar me-3">
                      <img src="{{$user->avatar}}" alt="avatar" class="rounded-circle" />
                  </div>
                  <div class="d-flex justify-content-between flex-grow-1">
                      <div class="me-2">
                          <p class="mb-0">{{$user->first_name. ' ' . $user->last_name}}</p>
                          <p class="mb-0 text-muted">{{$user->email}}</p>
                      </div>
                  </div>
                </li>
              @empty
              @endforelse
            </ul>
            {{--  --}}
            <div class="col-12 d-flex justify-content-between">
              <button type="button" class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
              </button>
              <button type="button" class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
            </div>
          </div>
          <!-- Application Submitters -->
          <div id="application-submitters" class="content">
            <div class="content-header mb-3">
              <h6 class="mb-0">Application Submitters</h6>
              <small>Manage Application Submitters.</small>
            </div>
            <hr>
            {{-- is_public --}}
            <div class="col-6 mt-4 mb-2">
              <label class="switch mt-3">
                {{ Form::checkbox('is_public', 1, !$application->company_id,['class' => 'switch-input'])}}
                <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
                </span>
                <span class="switch-label">Anyone?</span>
              </label>
            </div>

            {{-- select company --}}
            <div class="form-group mb-2 col-12 {{$application->company_id ?: 'd-none'}}">
              {{ Form::label('company_ids[]', __('Can Submit'), ['class' => 'col-form-label']) }}
              {!! Form::select('company_ids[]', $companies ?? [], $selectedCompany ?? null, [
              'data-placeholder' => __('Select Company'),
              'class' => 'form-select select2Remote',
              'data-url' => route('resource-select', ['groupedCompany']),
              'multiple' => 'multiple',
              ])!!}
            </div>

            <ul id="members-list" class="p-0 m-0">
              @forelse($application->submitters ?? [] as $user)
                <li class="d-flex flex-wrap mb-3" data-user-id="{{$user->id}}">
                  <span class="mx-3 pt-2 cursor-pointer" onclick="$(this).parents('li').remove();"><i class="mr-0 fa fa-xl fa-xmark"></i></span>
                  <input type="hidden" name="application_users[]" value="{{$user->id}}">
                  <div class="avatar me-3">
                      <img src="{{$user->avatar}}" alt="avatar" class="rounded-circle" />
                  </div>
                  <div class="d-flex justify-content-between flex-grow-1">
                      <div class="me-2">
                          <p class="mb-0">{{$user->first_name. ' ' . $user->last_name}}</p>
                          <p class="mb-0 text-muted">{{$user->email}}</p>
                      </div>
                  </div>
                </li>
              @empty
              @endforelse
            </ul>
            {{--  --}}
            <div class="col-12 d-flex justify-content-between">
              <button type="button" class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Previous</span>
              </button>
              <button type="submit" data-form="ajax-form" class="btn btn-primary btn-submit">{{ __('Save') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    {!! Form::close() !!}
  </div>
  <!-- /Vertical Wizard -->
@endsection
@push('style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
  <link href="{{ asset('vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" rel="stylesheet" />
  {{-- <link href="{{ asset('vendor/forms/css/style.css') }}" rel="stylesheet" /> --}}
  {{-- <link href="{{ asset('vendor/css/custom.css') }}" rel="stylesheet" /> --}}
  <style>
    .bootstrap-tagsinput {
        padding: 5px 10px;
        line-height: 28px;
        background: #f8f9fd;
        border: 1px solid #f1f1f1;
        border-radius: 10px;
        width: 100%;
    }

    .bootstrap-tagsinput .tag {
        background: var(--bs-primary);
        padding: 5px 12px;
        color: #fff;
        border-radius: 10px;
    }
  </style>
@endpush
@push('scripts')
  <script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
  <script src="{{ asset('assets/js/custom/select2.js') }}"></script>
  <script src="{{ asset('assets/js/custom/flatpickr.js') }}"></script>
  <script src="{{ asset('vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
  <script src="{{ asset('vendor/js/custom.js') }}"></script>
  <script>
    $(document).ready(function () {
      $(".inputtags").tagsinput('items');
    });
    // on change of is_public, show/hide company_id
    $(document).on('change', 'input[name="is_public"]', function() {
      if ($(this).is(':checked')) {
        $('select[name="company_ids[]"]').closest('.form-group').addClass('d-none');
      } else {
        $('select[name="company_ids[]"]').closest('.form-group').removeClass('d-none');
      }
    });

    // Vertical Wizard
    // --------------------------------------------------------------------
    const wizardVertical = document.querySelector('.wizard-vertical'),
      wizardVerticalBtnNextList = [].slice.call(wizardVertical.querySelectorAll('.btn-next')),
      wizardVerticalBtnPrevList = [].slice.call(wizardVertical.querySelectorAll('.btn-prev')),
      wizardVerticalBtnSubmit = wizardVertical.querySelector('.btn-submit');

    if (typeof wizardVertical !== undefined && wizardVertical !== null) {
      const verticalStepper = new Stepper(wizardVertical, {
        linear: false
      });
      if (wizardVerticalBtnNextList) {
        wizardVerticalBtnNextList.forEach(wizardVerticalBtnNext => {
          wizardVerticalBtnNext.addEventListener('click', event => {
            verticalStepper.next();
          });
        });
      }
      if (wizardVerticalBtnPrevList) {
        wizardVerticalBtnPrevList.forEach(wizardVerticalBtnPrev => {
          wizardVerticalBtnPrev.addEventListener('click', event => {
            verticalStepper.previous();
          });
        });
      }

      // if (wizardVerticalBtnSubmit) {
      //   wizardVerticalBtnSubmit.addEventListener('click', event => {
      //     alert('Submitted..!!');
      //   });
      // }
    }
  </script>
  <script>
    // Get the select element and members list
    const membersList = document.getElementById('members-list');
    // debugger;

    // Add a change event listener to the select element
     function addUser(event) {
        // Get the selected option and its data attributes
        const option = $(event).select2('data')[0];
        const name = option.full_name;
        const email = option.text;
        const avatar = option.avatar;
        const userId = option.id;

        const isUserAdded = [...membersList.children].some((li) => {
        const userIdAttr = li.getAttribute('data-user-id');
         return userIdAttr && userIdAttr === userId;
        });
        if(!isUserAdded){
        // Create a new li element with the selected user's information
        const li = document.createElement('li');
        li.className = 'd-flex flex-wrap mb-3';
        li.setAttribute('data-user-id', userId);
        li.innerHTML = `
        <span class="mx-3 pt-2 cursor-pointer" onclick="$(this).parents('li').remove();"><i class="mr-0 fa fa-xl fa-xmark"></i></span>
        <input type="hidden" name="application_users[]" value="${userId}">
            <div class="avatar me-3">
                <img src="${avatar}" alt="avatar" class="rounded-circle" />
            </div>
            <div class="d-flex justify-content-between flex-grow-1">
                <div class="me-2">
                    <p class="mb-0">${name}</p>
                    <p class="mb-0 text-muted">${email}</p>
                </div>
                <div class="dropdown">
                    <button
                        type="button"
                        class="btn dropdown-toggle p-2"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                    <span id="button-`+userId+`" class="text-muted fw-normal me-2 d-inline-block">{{$userRoles[array_keys($userRoles)[0]]}}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      @foreach($userRoles as $id => $role)
                        <li>
                          <a class="dropdown-item" href="javascript:updateRole({{$id}}, '{{$role}}', `+userId+`)" data-role="{{$id}}">{{$role}}</a>
                        </li>
                      @endforeach
                    </ul>
                </div>
            </div>
        `;

        membersList.appendChild(li);
      }
    }//);

    function updateRole(id, role, userId) {
      $('#button-'+userId).text(role);
    }

    // on change has_end_date, show/hide end_at
    $(document).on('change', 'input[name="has_end_date"]', function() {
      if ($(this).is(':checked')) {
        $('input[name="end_at"]').closest('.form-group').removeClass('d-none');
      } else {
        $('input[name="end_at"]').closest('.form-group').addClass('d-none');
      }
    });
  </script>
@endpush
