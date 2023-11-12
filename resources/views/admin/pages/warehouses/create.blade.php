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
    {!! Form::select('owner_type', [''=> 'Select Type', 'Company' => 'Company', 'PartnerCompany' => 'Partner', 'Client' => 'Client'], $ownerType ?? null, [
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
    <!-- Left Column -->
    <div class="col-md-6 col d-flex flex-column">

              {{-- Location Address --}}
              <div class="form-group">
                {{ Form::label('address', __('Location Address'), ['class' => 'col-form-label']) }}
                {!! Form::text('address', $address, ['id' => 'address', 'class' => 'form-control', 'placeholder' => __('Enter Address')]) !!}
              </div>
              
              <div class="form-group">
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
              <div class="form-group">
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
              <div class="form-group">
                {{ Form::label('city_id', __('City'), ['class' => 'col-form-label']) }}
                {!! Form::select('city_id', $cities ?? [], $warehouse->city_id, [
                'class' => 'form-select globalOfSelect2Remote dependent-select',
                'data-url' => route('resource-select', ['City']),
                'data-allow-clear' => 'true',
                'data-placeholder' => __('Select City'),
                'data-dependent_id' => 'user-state-id'
                ]) !!}
              </div>
              <div class="row"> <!-- Start of the row -->
              {{-- Latitude --}}
              <div class="form-group col-6"">
                {{ Form::label('latitude', __('Latitude'), ['class' => 'col-form-label']) }}
                {!! Form::number('latitude', $latitude, ['class' => 'form-control', 'placeholder' => __('Latitude')]) !!}
              </div>
            
              {{-- Longitude --}}
              <div class="form-group  col-6">
                {{ Form::label('longitude', __('Longitude'), ['class' => 'col-form-label']) }}
                {!! Form::number('longitude', $longitude, ['class' => 'form-control', 'placeholder' => __('Longitude')]) !!}
              </div>
            </div>
              {{-- Zoom Level --}}
              <div class="form-group">
                {{ Form::label('zoomLevel', __('Zoom Level'), ['class' => 'col-form-label']) }}
                {!! Form::number('zoomLevel', $zoomLevel, ['class' => 'form-control', 'placeholder' => __('Zoom Level')]) !!}
              </div>       

            
    </div>
  
    <!-- Right Column -->
    <div class="col-md-6 col d-flex flex-column">
        <!-- Map -->
        <div class="form-group mt-4 col d-flex flex-column" id="map" styless="height: 400px;"></div>
    </div>

  {{-- Status --}}
  <div class="form-group col-12">
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

