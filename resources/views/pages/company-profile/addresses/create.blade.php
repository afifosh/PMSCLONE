@if ($address->id)
    {!! Form::model($address, ['route' => ['company.addresses.update', $address->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($address, ['route' => ['company.addresses.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data', "autocomplete" => "off"]) !!}
@endif

<div class="row">
  <div class="form-group col-6">
      {{ Form::label('name', __('Address Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Address Name')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('country_id', __('Country'), ['class' => 'col-form-label']) }}
    {!! Form::select('country_id', $countries->prepend('Select Country', ''), null, ['class' => 'form-controll globalOfSelect2']) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('address_line_1', __('Address Line 1'), ['class' => 'col-form-label']) }}
    {!! Form::text('address_line_1', null, ['class' => 'form-control', 'placeholder' => __('Address Line 1')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('address_line_2', __('Address Line 2'), ['class' => 'col-form-label']) }}
    {!! Form::text('address_line_2', null, ['class' => 'form-control', 'placeholder' => __('Address Line 2')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('address_line_3', __('Address Line 3'), ['class' => 'col-form-label']) }}
    {!! Form::text('address_line_3', null, ['class' => 'form-control', 'placeholder' => __('Address Line 3')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('website', __('Website'), ['class' => 'col-form-label']) }}
    {!! Form::text('website', null, ['class' => 'form-control', 'placeholder' => __('Website URL')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('city', __('City/Town/Locality'), ['class' => 'col-form-label']) }}
    {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('City/Town/Locality')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
    {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('State')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('province', __('Province'), ['class' => 'col-form-label']) }}
    {!! Form::text('province', null, ['class' => 'form-control', 'placeholder' => __('Province')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('postal_code', __('Postal Code'), ['class' => 'col-form-label']) }}
    {!! Form::text('postal_code', null, ['class' => 'form-control', 'placeholder' => __('Postal Code')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
    {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => __('Phone')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('fax', __('Fax'), ['class' => 'col-form-label']) }}
    {!! Form::text('fax', null, ['class' => 'form-control', 'placeholder' => __('Fax')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('email', __('Email'), ['class' => 'col-form-label']) }}
    {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Email')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('zip', __('Zip Code'), ['class' => 'col-form-label']) }}
    {!! Form::text('zip', null, ['class' => 'form-control', 'placeholder' => __('Zip Code')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('latitude', __('Latitude'), ['class' => 'col-form-label']) }}
    {!! Form::text('latitude', null, ['class' => 'form-control', 'placeholder' => __('Latitude')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('longitude', __('Longitude'), ['class' => 'col-form-label']) }}
    {!! Form::text('longitude', null, ['class' => 'form-control', 'placeholder' => __('Longitude')]) !!}
  </div>
  <div class="d-flex justify-content-end">
    <div class="ps-2">
      {!! Form::checkbox('address_type', 'purchasing', false, ['class' => 'form-check-input']) !!}
      {!! Form::label('', 'Purchasing Address', ['class' => 'form-check-label']) !!}
    </div>
    <div class="ps-2">
      {!! Form::checkbox('address_type', 'billing', false, ['class' => 'form-check-input']) !!}
      {!! Form::label('', 'Payment Address', ['class' => 'form-check-label']) !!}
    </div>
    <div class="ps-2">
      {!! Form::checkbox('address_type', 'rfp_only', false, ['class' => 'form-check-input']) !!}
      {!! Form::label('', 'RFP Only Address', ['class' => 'form-check-label']) !!}
    </div>
  </div>
</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
