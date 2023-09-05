@if ($user->id)
    {!! Form::model($user, ['route' => ['admin.companies.contacts.update', [$company, $user]], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($user, ['route' => ['admin.companies.contacts.store', [$company]], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif
<div class="row">
    <div class="form-group col-6">
        {{ Form::label('first_name', __('First Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => __('First Name')]) !!}
    </div>
    <div class="form-group col-6">
      {{ Form::label('last_name', __('Last Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('last_name', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Last Name')]) !!}
    </div>
    <div class="form-group col-6">
        {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
        {!! Form::text('email', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Enter Email Address')]) !!}
    </div>
    <div class="form-group col-6">
      {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
      <br>
      <input type="text" value="{{$user->phone}}" name="phone" class='form-control ignore-ajax-error w-100', placeholder={{__('Phone')}}, id='phone'>
      <span id="itiPhone"></span>
      <input type="hidden" id="itiPhoneCountry" class="ignore-ajax-error" name="phone_country">
    </div>
    {{-- country --}}
    <div class="form-group col-6">
      {{ Form::label('country_id', __('Country'), ['class' => 'col-form-label']) }}
      {!! Form::select('country_id', $countries, null, [
      'class' => 'form-select globalOfSelect2Remote',
      'data-url' => route('resource-select', ['Country']),
      'id' => 'user-countries-id',
      ]) !!}
    </div>
    {{-- states --}}
    <div class="form-group col-6">
      {{ Form::label('state_id', __('State'), ['class' => 'col-form-label']) }}
      {!! Form::select('state_id', $states, null, [
      'class' => 'form-select globalOfSelect2Remote',
      'data-url' => route('resource-select', ['State']),
      'data-dependent_id' => 'user-countries-id',
      'id' => 'user-state-id',
      ]) !!}
    </div>
    {{-- cities --}}
    <div class="form-group col-6">
      {{ Form::label('city_id', __('City'), ['class' => 'col-form-label']) }}
      {!! Form::select('city_id', $cities, null, [
      'class' => 'form-select globalOfSelect2Remote',
      'data-url' => route('resource-select', ['City']),
      'data-dependent_id' => 'user-state-id'
      ]) !!}
    </div>
  <div class="form-group col-6">
    {{ Form::label('roles', __('Role'), ['class' => 'col-form-label']) }}
    {!! Form::select('roles', $roles, $user->roles, ['class' => 'form-select globalOfSelect2']) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('job_title', __('Job Title'), ['class' => 'col-form-label']) }}
    {!! Form::text('job_title', null, ['class' => 'form-control', 'placeholder' => __('Job Title')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
  @php
  $enum = $user::getPossibleEnumValues('status');
  @endphp
    {!! Form::select('status', array_combine($enum, array_map('ucwords',$enum)), $user->status, ['class' => 'form-select globalOfSelect2']) !!}
  </div>
  <div>
    <div class="form-check col-12 mt-2">
      <input class="form-check-input" type="checkbox" value="1" disabled>
      <label class="form-check-label">
        Mail Credentials to User
      </label>
    </div>
  </div>
  <hr class="mt-3">
  <div class="form-group col-6">
    <label class="switch d-flex flex-column">
      {{ Form::label('email_verified_at', __('Verified'), ['class' => 'col-form-label']) }}
      {{ Form::checkbox('email_verified_at', 1, $user->email_verified_at,['class' => 'switch-input is-invalid'])}}
      {{-- <input type="checkbox" class="switch-input is-invalid" checked /> --}}
      <span class="switch-toggle-slider position-relative mt-2">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label"></span>
    </label>
  </div>
  <div class="form-group col-6">
    <label class="switch d-flex flex-column">
      {{ Form::label('can_login', __('Can Login'), ['class' => 'col-form-label']) }}
      {{ Form::checkbox('can_login', 1, $user->can_login,['class' => 'switch-input is-invalid'])}}
      <span class="switch-toggle-slider position-relative mt-2">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label"></span>
    </label>
  </div>
  <hr class="mt-3">
  <div class="form-group col-6">
    {{ Form::label('password', __('Password'), ['class' => 'col-form-label']) }}
    <div class="form-check">
      <input class="form-check-input pass-gen" type="radio" name="pass-gen" value="auto" id="auto-gen-pass" checked>
      <label class="form-check-label" for="auto-gen-pass">
        Auto Genefault Password
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input pass-gen" type="radio" value="manually" name="pass-gen" id="manual-pass">
      <label class="form-check-label" for="manual-pass">
        Add Manually
      </label>
    </div>
    {!! Form::password('password', ['class' => 'form-control d-none', 'placeholder' => __('******')]) !!}
  </div>
  <div class="col-6">
    {!! Form::label('gender', 'Gender', []) !!}
    <div class="d-flex">
      <div class="form-check">
        <input class="form-check-input" type="radio" name="gender" value="Male" id="gender-m" @checked($user->gender == 'Male')>
        <label class="form-check-label me-3" for="gender-m">
          Male
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="gender" value="Female" id="gender-f" @checked($user->gender == 'Female')>
        <label class="form-check-label me-3" for="gender-f">
          Female
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="gender" value="Other" id="gender-o" @checked($user->gender == 'Other')>
        <label class="form-check-label me-3" for="gender-o">
          Other
        </label>
      </div>
    </div>
  </div>
</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{$user->id ?  __('Update')  :  __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
<script>
  $(document).on('change', '.pass-gen', function(){
    if($(this).attr('id') == 'auto-gen-pass'){
      $('#password').addClass('d-none');
      $('#password').val('');
    }else{
      $('#password').removeClass('d-none');
    }
  })
</script>

