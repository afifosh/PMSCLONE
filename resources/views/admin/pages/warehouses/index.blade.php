@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Warehouses')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<style>
  .pac-container {
      z-index: 10000 !important;
}
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>

@endsection

@section('content')
<h4 class="fw-semibold mb-4">{{__('Warehouses')}}</h4>

<div class="mt-3  col-12">
  <div class="card">
    <div class="card-body">
      {{$dataTable->table()}}
    </div>
  </div>
</div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3moRzASD0M20O8aMwMwBPro3arMTeJes&libraries=places" async defer></script>

<script>

var map;
var marker;
var autocomplete;
var locationInfo = {};

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 24.7136, lng: 46.6753 }, // Coordinates for Riyadh
        zoom: 6 // You can adjust the zoom level as needed
    });

    marker = new google.maps.Marker({
        position: { lat: 24.7136, lng: 46.6753 }, // Marker position set to Riyadh
        map: map,
        draggable: true // Marker is draggable
    });

    var options = {
    types: [],
    componentRestrictions: {country: 'sa'}
    };

    autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'), options);
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        map.setCenter(place.geometry.location);
        map.setZoom(17);
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        updateLocationInfo(place);
    });

    google.maps.event.addListener(marker, 'dragend', function() {
        var markerPos = marker.getPosition();
        map.setCenter(markerPos);

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'location': markerPos}, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    updateLocationInfo(results[0]);
                    document.getElementById('address').value = results[0].formatted_address;
                } else {
                    window.alert('No results found');
                }
            } else {
                window.alert('Geocoder failed due to: ' + status);
            }
        });
    });

    map.addListener('click', function(e) {
        marker.setPosition(e.latLng);
        map.panTo(e.latLng);

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({'location': e.latLng}, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    updateLocationInfo(results[0]);
                    document.getElementById('address').value = results[0].formatted_address;
                } else {
                    window.alert('No results found');
                }
            } else {
                window.alert('Geocoder failed due to: ' + status);
            }
        });
    });
}

function updateLocationInfo(place) {
    var addressComponents = place.address_components,
        latLng = place.geometry.location;

    locationInfo = {
        geo: [latLng.lat(), latLng.lng()],
        country: null,
        state: null,
        city: null,
        postalCode: null,
        street: null,
        streetNumber: null
    };

        // Update the latitude and longitude input fields
        document.querySelector('[name="latitude"]').value = latLng.lat();
    document.querySelector('[name="longitude"]').value = latLng.lng();

    addressComponents.forEach(function(component) {
        var type = component.types[0];
        switch (type) {
            case "country":
                locationInfo.country = component.long_name;
                break;
            case "administrative_area_level_1":
                locationInfo.state = component.long_name;
                break;
            case "locality":
                locationInfo.city = component.long_name;
                break;
            case "postal_code":
                locationInfo.postalCode = component.long_name;
                break;
            case "route":
                locationInfo.street = component.long_name;
                break;
            case "street_number":
                locationInfo.streetNumber = component.long_name;
                break;
        }
    });

    // document.getElementById('js-preview-json').textContent = JSON.stringify(locationInfo, null, 4);
}


function updateMapAndAddress(lat, lng) {
    var newLatLng = new google.maps.LatLng(lat, lng);
    marker.setPosition(newLatLng);
    map.setCenter(newLatLng);

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({'location': newLatLng}, function(results, status) {
        if (status === 'OK') {
            if (results[0]) {
                document.getElementById('address').value = results[0].formatted_address;
                updateLocationInfo(results[0]);
            }
        } else {
            console.error('Geocoder failed due to: ' + status);
        }
    });
}


$('#globalModal').on('shown.bs.modal', function () {
    initMap(); 
    google.maps.event.trigger(map, 'resize');
    map.setCenter(marker.getPosition()); 
    document.querySelector('[name="latitude"]').addEventListener('change', function() {
    var lat = parseFloat(this.value);
    var lng = parseFloat(document.querySelector('[name="longitude"]').value);
    if (!isNaN(lat) && !isNaN(lng)) {
        updateMapAndAddress(lat, lng);
    }
});

document.querySelector('[name="longitude"]').addEventListener('change', function() {
    var lng = parseFloat(this.value);
    var lat = parseFloat(document.querySelector('[name="latitude"]').value);
    if (!isNaN(lat) && !isNaN(lng)) {
        updateMapAndAddress(lat, lng);
    }
});

});

</script>
@endpush
