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
function initMap() {
  var map = new google.maps.Map(document.getElementById('map'), {
    center: { lat: 24.7136, lng: 46.6753 }, // Coordinates for Riyadh
    zoom: 6 // You can adjust the zoom level as needed
});
    var input = document.getElementById('address-input');
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);


var marker = new google.maps.Marker({
    position: { lat: 24.7136, lng: 46.6753 }, // Marker position set to Riyadh
    map: map,
    draggable: true // Marker is draggable
});

    autocomplete.addListener('place_changed', function() {
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        // If the place has a geometry, present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        document.getElementById('address-latitude').value = place.geometry.location.lat();
        document.getElementById('address-longitude').value = place.geometry.location.lng();
    });

    // Allow user to drag the marker and update the position
    google.maps.event.addListener(marker, 'dragend', function() {
        document.getElementById('address-latitude').value = marker.getPosition().lat();
        document.getElementById('address-longitude').value = marker.getPosition().lng();
    });
}

// Join the stage channel
$('#globalModal').on('shown.bs.modal', function (e) {
  initMap(); // Initialize your map here
    google.maps.event.trigger(map, 'resize'); // Triggers resize event to ensure map is displayed correctly
  
});
</script>
@endpush
