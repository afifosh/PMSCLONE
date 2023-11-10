@if ($warehouse->id)
  {!! Form::model($warehouse, ['route' => ['admin.warehouses.update', $warehouse], 'method' => 'PUT']) !!}
@else
  {!! Form::model($warehouse, ['route' => ['admin.warehouses.store'], 'method' => 'POST']) !!}
@endif
<div class="row">
  {{-- Warehouse name --}}
  <div class="form-group col-12">
      {{ Form::label('name', __('Warehouse Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Warehouse Name')]) !!}
  </div>
  {{-- Owner Type --}}
  <div class="form-group col-6">
    {{ Form::label('owner_type', __('Owner Type'), ['class' => 'col-form-label']) }}
    {!! Form::select('owner_type', [''=> 'Select Type', 'Company' => 'Company', 'PartnerCompany' => 'Partner', 'Client' => 'Client'], $warehouse->owner_type ?? null, [
    'class' => 'form-select globalOfSelect2 dependent-select',
    'id' => 'warehouse-owner-type-id',
    ]) !!}
  </div>
  {{-- Owner --}}
  <div class="form-group col-6">
    {{ Form::label('owner_id', __('Owner'), ['class' => 'col-form-label']) }}
    {!! Form::select('owner_id', $owners ?? [], $warehouse->owner_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Owner']),
    'data-dependent_id' => 'warehouse-owner-type-id',
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select Owner'),
    'id' => 'warehouse-owner-id',
    ]) !!}
  </div>  

  <div class="form-group mb-4">
    <label for="address-input">Address</label>
    <input type="text" id="address-input" name="address_address" class="form-control">
    <input type="hidden" name="address_latitude" id="address-latitude" />
    <input type="hidden" name="address_longitude" id="address-longitude" />
</div>
<div class="form-group mt-4" id="map" style="height: 400px;"></div>
  <div class="form-group col-6">
    {{ Form::label('country_id', __('Country'), ['class' => 'col-form-label']) }}
    {!! Form::select('country_id', $countries ?? [], $warehouse->country_id, [
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
    {!! Form::select('state_id', $states ?? [], $warehouse->state_id, [
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
    {!! Form::select('city_id', $cities ?? [], $warehouse->city_id, [
    'class' => 'form-select globalOfSelect2Remote dependent-select',
    'data-url' => route('resource-select', ['City']),
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select City'),
    'data-dependent_id' => 'user-state-id'
    ]) !!}
  </div>
  {{-- Location Name --}}
  <div class="form-group col-6">
    {{ Form::label('location_name', __('Location Name'), ['class' => 'col-form-label']) }}
    {!! Form::text('location_name', $location_name, ['class' => 'form-control', 'placeholder' => __('Location Name')]) !!}
  </div>

  {{-- Address --}}
  <div class="form-group col-6">
    {{ Form::label('address', __('Location Address'), ['class' => 'col-form-label']) }}
    {!! Form::text('address', $address, ['class' => 'form-control', 'placeholder' => __('Location Address')]) !!}
  </div>

  {{-- Latitude --}}
  <div class="form-group col-6">
    {{ Form::label('latitude', __('Latitude'), ['class' => 'col-form-label']) }}
    {!! Form::number('latitude', $latitude, ['class' => 'form-control', 'placeholder' => __('Latitude')]) !!}
  </div>

  {{-- Longitude --}}
  <div class="form-group col-6">
    {{ Form::label('longitude', __('Longitude'), ['class' => 'col-form-label']) }}
    {!! Form::number('longitude', $longitude, ['class' => 'form-control', 'placeholder' => __('Longitude')]) !!}
  </div>

  {{-- Zoom Level --}}
  <div class="form-group col-6">
    {{ Form::label('zoomLevel', __('Zoom Level'), ['class' => 'col-form-label']) }}
    {!! Form::number('zoomLevel', $zoomLevel, ['class' => 'form-control', 'placeholder' => __('Zoom Level')]) !!}
  </div>


  {{-- Status --}}
  <div class="form-group col-6">
    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
    {!! Form::select('status', ['Active' => 'Active', 'Inactive' => 'Inactive'], $warehouse->status, [
    'class' => 'form-select globalOfSelect2',
    ]) !!}
  </div>
  {{-- Additional fields as required --}}
  <div class="mt-3 d-flex justify-content-end">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
  </div>
{!! Form::close() !!}

