@if ($user->id)
    {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($user, ['route' => ['users.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
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
    {!! Form::text('phone', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Phone Number')]) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('password', __('Password'), ['class' => 'col-form-label']) }}
    {!! Form::password('password', ['class' => 'form-control', 'required' => isset($user->id), 'placeholder' => __('******')]) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('password_confirmation', __('Confirm Password'), ['class' => 'col-form-label']) }}
    {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => __('******')]) !!}
  </div>
  {{--

    {{ Form::label('avatar', __('Avatar'), ['class' => '']) }}

    {!! Form::file('avatar', ['class' => 'form-control', 'accept'=> 'image/*']) !!}
</div> --}}
<div class="form-group col-6">
    {{ Form::label('roles', __('Role'), ['class' => 'col-form-label']) }}
    {!! Form::select('roles', $roles, $user->roles, ['class' => 'form-select globalOfSelect2']) !!}
</div>

<div class="form-group col-6">
  @php
    $enum = $user::getPossibleEnumValues('status');
  @endphp
  {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
  {!! Form::select('status', array_combine($enum, array_map('ucwords',$enum)), $user->status, ['class' => 'form-select globalOfSelect2']) !!}
</div>

</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
