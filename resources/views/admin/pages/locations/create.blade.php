@if ($location->id)
  {!! Form::model($location, ['route' => ['admin.locations.update', ['location' => $location]],
      'method' => 'PUT',
      'data-stage-id' => $location->id,
  ]) !!}
@else
  {!! Form::model($location, ['route' => ['admin.locations.store'], 'method' => 'POST']) !!}
@endif
<div class="row">
  {{-- name --}}
  <div class="form-group col-6">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>
  {{-- country --}}
  <div class="form-group col-6">
    {{ Form::label('country_id', __('Country'), ['class' => 'col-form-label']) }}
    {!! Form::select('country_id', $countries ?? [], $location->country_id, [
    'class' => 'form-select globalOfSelect2Remote dependent-select',
    'data-url' => route('resource-select', ['Country']),
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select Country'),
    'id' => 'user-countries-id',
    ]) !!}
  </div>
  {{-- states --}}
  <div class="form-group col-6">
    {{ Form::label('state_id', __('State'), ['class' => 'col-form-label']) }}
    {!! Form::select('state_id', $states ?? [], $location->state_id, [
    'class' => 'form-select globalOfSelect2Remote dependent-select',
    'data-url' => route('resource-select', ['State']),
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select State'),
    'data-dependent_id' => 'user-countries-id',
    'id' => 'user-state-id',
    ]) !!}
  </div>
  {{-- cities --}}
  <div class="form-group col-6">
    {{ Form::label('city_id', __('City'), ['class' => 'col-form-label']) }}
    {!! Form::select('city_id', $cities ?? [], $location->city_id, [
    'class' => 'form-select globalOfSelect2Remote dependent-select',
    'data-url' => route('resource-select', ['City']),
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select City'),
    'data-dependent_id' => 'user-state-id'
    ]) !!}
  </div>
  {{-- Address --}}
  <div class="form-group col-6">
    {{ Form::label('address', __('Address'), ['class' => 'col-form-label']) }}
    {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => __('Address')]) !!}
  </div>
  {{-- Latitude --}}
  <div class="form-group col-6">
    {{ Form::label('latitude', __('Latitude'), ['class' => 'col-form-label']) }}
    {!! Form::number('latitude', null, ['class' => 'form-control', 'placeholder' => __('Latitude')]) !!}
  </div>
  {{-- Longitude --}}
  <div class="form-group col-6">
    {{ Form::label('longitude', __('Longitude'), ['class' => 'col-form-label']) }}
    {!! Form::number('longitude', null, ['class' => 'form-control', 'placeholder' => __('Longitude')]) !!}
  </div>
  {{-- Zoom Level --}}
  <div class="form-group col-6">
    {{ Form::label('zoomLevel', __('Zoom Level'), ['class' => 'col-form-label']) }}
    {!! Form::number('zoomLevel', null, ['class' => 'form-control', 'placeholder' => __('Zoom Level')]) !!}
  </div>
  {{-- Owner Type --}}
  <div class="form-group col-6">
    {{ Form::label('owner_type', __('Owner Type'), ['class' => 'col-form-label']) }}
    {!! Form::select('owner_type', [''=> 'Select Type', 'Company' => 'Company', 'PartnerCompany' => 'Partner', 'Client' => 'Client'], $ownerType ?? null, [
    'class' => 'form-select globalOfSelect2 dependent-select',
    'id' => 'location-owner-type-id',
    ]) !!}
  </div>
  {{-- Owner --}}
  <div class="form-group col-6">
    {{ Form::label('owner_id', __('Owner'), ['class' => 'col-form-label']) }}
    {!! Form::select('owner_id', $owners ?? [], $location->owner_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Owner']),
    'data-dependent_id' => 'location-owner-type-id',
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select Owner'),
    'id' => 'location-owner-id',
    ]) !!}
  </div>
  {{-- Status --}}
  <div class="form-group col-6">
    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
    {!! Form::select('status', ['Active' => 'Active', 'Inactive' => 'Inactive'], $location->status, [
    'class' => 'form-select globalOfSelect2',
    ]) !!}
  </div>
  {{-- is_public --}}
  <div class="col-12 my-2 mt-3">
    <label class="switch">
      {{ Form::checkbox('is_public', 1, $location->is_public,['class' => 'switch-input'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Is Public?</span>
    </label>
  </div>
  {{-- is_warehouse --}}
  <div class="col-12 my-2">
    <label class="switch">
      {{ Form::checkbox('is_warehouse', 1, $location->is_warehouse,['class' => 'switch-input'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Is Warehouse?</span>
    </label>
  </div>
<div class="mt-3 d-flex justify-content-end">
  <div class="btn-flt float-end">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
  </div>
</div>
{!! Form::close() !!}
