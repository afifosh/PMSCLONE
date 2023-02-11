@if ($user->id)
    {!! Form::model($user, ['route' => ['admin.users.update', $user->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($user, ['route' => ['admin.users.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif
<div class="row">
    <div class="form-group">
        {{ Form::label('first_name', __('First Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => __('First Name')]) !!}
    </div>
    <div class="form-group">
      {{ Form::label('last_name', __('Last Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('last_name', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Last Name')]) !!}
    </div>
    <div class="form-group">
        {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
        {!! Form::text('email', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Enter Email Address')]) !!}
    </div>
    <div class="form-group">
      {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
      {!! Form::text('phone', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Phone Number')]) !!}
  </div>
  <div class="form-group">
    {{ Form::label('password', __('Password'), ['class' => 'col-form-label']) }}
    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => __('******')]) !!}
  </div>
  <div class="form-group">
    {{ Form::label('password_confirmation', __('Confirm Password'), ['class' => 'col-form-label']) }}
    {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => __('******')]) !!}
  </div>
  {{-- <div class="form-group">

    {{ Form::label('avatar', __('Avatar'), ['class' => '']) }}

    {!! Form::file('avatar', ['class' => 'form-control', 'accept'=> 'image/*']) !!}
</div> --}}
</div>
  <div class="form-group">
      {{ Form::label('company_id', __('Organization'), ['class' => 'col-form-label']) }}
      {!! Form::select('company_id', $companies, @$user->designation->department->company_id, [
        'class' => 'form-select globalOfSelect2',
        'data-updateOptions' => 'ajax-options',
        'data-href' => route('admin.partner.departments.getByCompany'),
        'data-target' => '#add-user-company-departments'
        ]) !!}
  </div>
  <div class="form-group">
    {{ Form::label('department_id', __('Department'), ['class' => 'col-form-label']) }}
    {!! Form::select('department_id', $departments, @$user->designation->department_id, [
      'class' => 'form-select globalOfSelect2',
      'id' => 'add-user-company-departments',
      'data-updateOptions' => 'ajax-options',
      'data-href' => route('admin.partner.designations.getByDepartment'),
      'data-target' => '#add-user-company-designations'
      ]) !!}
  </div>
  <div class="form-group">
    {{ Form::label('designation_id', __('Designation'), ['class' => 'col-form-label']) }}
    {!! Form::select('designation_id', $designations, $user->designation_id, ['class' => 'form-select globalOfSelect2', 'id' => 'add-user-company-designations']) !!}
  </div>
  <div class="form-group">
    {{ Form::label('roles', __('Role'), ['class' => 'col-form-label']) }}
    {!! Form::select('roles[]', $roles, $user->roles, ['class' => 'form-select globalOfSelect2', 'multiple' => 'multiple']) !!}
  </div>
<div class="form-group">
  {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
@php
$enum = $user::getPossibleEnumValues('status');
@endphp
  {!! Form::select('status', array_combine($enum, $enum), $user->status, ['class' => 'form-select globalOfSelect2']) !!}
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
