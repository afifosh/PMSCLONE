@if ($client->id)
    {!! Form::model($client, ['route' => ['admin.clients.update', $client->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($client, ['route' => ['admin.clients.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
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
    {{ Form::label('address', __('Address'), ['class' => 'col-form-label']) }}
    {!! Form::text('address', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Address')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
    {!! Form::text('state', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('State')]) !!}
  </div>
  <div class="form-group col-6">
    {{ Form::label('zip_code', __('Zip Code'), ['class' => 'col-form-label']) }}
    {!! Form::text('zip_code', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Zip Code')]) !!}
  </div>

  <div class="form-group col-md-6">
    <label class="col-form-label" for="country">Country</label>
    <select id="country" name="country_id" class="globalOfSelect2 form-select">
      <option value="">Select</option>
      @forelse ($countries as $country)
          <option value="{{ $country->id }}" {{old('country_id') == $country->id || $country->id == $client->country_id ? 'selected' : '' }}>{{ $country->name }}</option>
      @empty
      @endforelse
    </select>
  </div>
  <div class="form-group col-md-6">
    <label class="col-form-label" for="language">Language</label>
    <select id="language" name="language" class="globalOfSelect2 form-select">
      <option value="">Select Language</option>
      @forelse ($languages as $key => $lang)
          <option value="{{ $key }}" {{old('language') == $key || $key == $client->language ? 'selected' : '' }}>{{ $lang }}</option>
      @empty
      @endforelse
    </select>
  </div>
  <div class="form-group col-md-6">
    <label class="col-form-label" for="timeZones">Timezone</label>
    <select id="timeZones" name="timezone" class="globalOfSelect2 form-select">
      <option value="">Select Timezone</option>
      @forelse ($timezones as $tz)
          <option value="{{ $tz['value'] }}" {{ $tz['value'] == $client->timezone ? 'selected' : ''}}>{{ $tz['label'] }}</option>
      @empty
      @endforelse
    </select>
  </div>
  <div class="form-group col-md-6">
    <label class="col-form-label" for="currency">Currency</label>
    <select id="currency" name="currency" class="globalOfSelect2 form-select">
      <option value="">Select Currency</option>
      @forelse ($currencies as $key => $cur)
          <option value="{{ $key }}" {{$key == $client->currency ? 'selected' : '' }}>{{ $cur }}</option>
      @empty
      @endforelse
    </select>
  </div>
<div class="form-group col-6">
  {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
@php
$enum = $client::getPossibleEnumValues('status');
@endphp
  {!! Form::select('status', array_combine($enum, $enum), $client->status, ['class' => 'form-select globalOfSelect2']) !!}
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
