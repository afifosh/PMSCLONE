{!! Form::model($user, ['route' => ['admin.users.updatePassword', $user->id], 'method' => 'PUT']) !!}
<div class="row">
    <div class="form-group col-6">
        {{ Form::label('first_name', __('First Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('first_name', null, ['class' => 'form-control', 'disabled'=>'disabled', 'placeholder' => __('First Name')]) !!}
    </div>
    <div class="form-group col-6">
      {{ Form::label('last_name', __('Last Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('last_name', null, ['class' => 'form-control', 'disabled'=>'disabled', 'required'=> 'true', 'placeholder' => __('Last Name')]) !!}
    </div>
    <div class="form-group col-12">
        {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
        {!! Form::text('email', null, ['class' => 'form-control', 'disabled'=>'disabled', 'required'=> 'true', 'placeholder' => __('Enter Email Address')]) !!}
    </div>
  <div class="form-group col-6">
    {{ Form::label('password', __('Password'), ['class' => 'col-form-label']) }}
    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => __('******')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('password_confirmation', __('Confirm Password'), ['class' => 'col-form-label']) }}
    {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => __('******')]) !!}
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
