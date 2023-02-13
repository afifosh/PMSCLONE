@if ($contactPerson->id)
    {!! Form::model($contactPerson, ['route' => ['admin.companies.contact-persons.update', $contactPerson->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($contactPerson, ['route' => ['admin.company-invitations.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', "autocomplete" => "off"]) !!}
@endif

<div class="row">
    <div class="form-group col-6">
        {{ Form::label('first_name', __('First Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('first_name', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('First Name')]) !!}
    </div>
    <div class="form-group col-6">
      {{ Form::label('last_name', __('Last Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('last_name', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Last Name')]) !!}
    </div>
    <div class="form-group col-6">
        {{ Form::label('email', __('User Email'), ['class' => 'col-form-label']) }}
        {!! Form::text('email', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Enter Email Address')]) !!}
    </div>
    <div class="form-group col-6">
      {{ Form::label('expiry_time', __('Invitation Expiry'), ['class' => 'col-form-label']) }}
      {!! Form::text('expiry_time', null, ['class' => 'form-control flatpickr', 'data-flatpickr' => '{"enableTime":true, "dateFormat": "Y-m-d H:i"}', 'required'=> 'true']) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('company_id', __('Company'), ['class' => 'col-form-label']) }}
    {!! Form::select('company_id', $companies, '', ['class' => 'form-select globalOfSelect2']) !!}
  </div>

  <div class="form-group col-6">
    {{ Form::label('role', __('Role'), ['class' => 'col-form-label']) }}
    {!! Form::select('role', $roles, '', ['class' => 'form-select globalOfSelect2']) !!}
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
