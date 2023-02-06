@if ($contactPerson->id)
    {!! Form::model($contactPerson, ['route' => ['admin.companies.contact-persons.update', $contactPerson->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($contactPerson, ['route' => ['admin.companies.contact-persons.store', $company], 'method' => 'POST', 'enctype' => 'multipart/form-data', "autocomplete" => "off"]) !!}
@endif

<div class="row">
    <div class="form-group">
        {{ Form::label('email', __('User Email'), ['class' => 'col-form-label']) }}
        {!! Form::text('email', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Enter Email Address')]) !!}
    </div>
    <div class="form-group">
      {{ Form::label('expiry_time', __('Invitation Expiry'), ['class' => 'col-form-label']) }}
      {!! Form::text('expiry_time', null, ['class' => 'form-control flatpickr', 'data-flatpickr' => '{"enableTime":true, "dateFormat": "Y-m-d H:i"}', 'required'=> 'true']) !!}
  </div>

  <div class="form-group">
    {{ Form::label('role', __('Role'), ['class' => 'col-form-label']) }}
    {!! Form::select('role', $roles, '', ['class' => 'form-select globalOfSelect2']) !!}
  </div>
    {{-- <div class="form-group">

    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
    @php
      $enum = $company::getPossibleEnumValues('status');
    @endphp
        {!! Form::select('status', array_combine($enum, $enum), $company->status, ['class' => 'form-select globalOfSelect2']) !!}
    </div> --}}
  {{-- <div class="form-group">

    {{ Form::label('avatar', __('Avatar'), ['class' => '']) }}

    {!! Form::file('avatar', ['class' => 'form-control', 'accept'=> 'image/*']) !!}
</div> --}}
</div>
{{-- <div class="form-group">

    {{ Form::label('roles', __('Role'), ['class' => 'col-form-label']) }}


    {!! Form::select('roles[]', $roles, $user->roles, ['class' => 'form-select globalOfSelect2', 'multiple' => 'multiple']) !!}
</div> --}}
<div class="modal-footer">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
