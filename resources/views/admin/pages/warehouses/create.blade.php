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

    {{-- Address --}}
    <div class="form-group col-12">
      {{ Form::label('address_address', 'Address', ['class' => 'col-form-label']) }}
      {{ Form::text('address_address', null, ['class' => 'form-control map-input', 'id' => 'address-input']) }}
    </div>

    {{-- Latitude --}}
    {{ Form::hidden('address_latitude', '0', ['id' => 'address-latitude']) }}

    {{-- Longitude --}}
    {{ Form::hidden('address_longitude', '0', ['id' => 'address-longitude']) }}

  {{-- map --}}
  <div class="form-group col-12 mt-2">
      <div id="address-map-container" style="width:100%;height:400px; ">
        <div style="width: 100%; height: 100%" id="address-map"></div>
    </div>
  </div>
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3moRzASD0M20O8aMwMwBPro3arMTeJes&libraries=places&callback=initialize" async defer></script>

<script>
function initialize() {

$('form').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
        return false;
    }
});
const locationInputs = document.getElementsByClassName("map-input");

const autocompletes = [];
const geocoder = new google.maps.Geocoder;
for (let i = 0; i < locationInputs.length; i++) {

    const input = locationInputs[i];
    const fieldKey = input.id.replace("-input", "");
    const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

    const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -33.8688;
    const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 151.2195;

    const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
        center: {lat: latitude, lng: longitude},
        zoom: 13
    });
    const marker = new google.maps.Marker({
        map: map,
        position: {lat: latitude, lng: longitude},
    });

    marker.setVisible(isEdit);

    const autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.key = fieldKey;
    autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
}

for (let i = 0; i < autocompletes.length; i++) {
    const input = autocompletes[i].input;
    const autocomplete = autocompletes[i].autocomplete;
    const map = autocompletes[i].map;
    const marker = autocompletes[i].marker;

    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        marker.setVisible(false);
        const place = autocomplete.getPlace();

        geocoder.geocode({'placeId': place.place_id}, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                const lat = results[0].geometry.location.lat();
                const lng = results[0].geometry.location.lng();
                setLocationCoordinates(autocomplete.key, lat, lng);
            }
        });

        if (!place.geometry) {
            window.alert("No details available for input: '" + place.name + "'");
            input.value = "";
            return;
        }

        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

    });
}
}

function setLocationCoordinates(key, lat, lng) {
const latitudeField = document.getElementById(key + "-" + "latitude");
const longitudeField = document.getElementById(key + "-" + "longitude");
latitudeField.value = lat;
longitudeField.value = lng;
}
</script>

