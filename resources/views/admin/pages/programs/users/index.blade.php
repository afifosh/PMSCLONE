@extends('admin/layouts/layoutMaster')

@section('title', $program->name.' - Program')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection

@section('content')
{{-- <h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">User Profile /</span> Profile
</h4> --}}
@include('admin.pages.programs.program-nav', ['tab' => 'users'])
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
  <script>
$(document).ready(function() {
    $(document).on('change', '#permanent_access', function() {
      if ($(this).is(':checked')) {
          $('#date_input').addClass('d-none'); // Hide the date input div
          $('#until_at').prop('disabled', true); // Disable the date input
          $('#until_at').val('');
      } else {
           $('#date_input').removeClass('d-none');
           $('#until_at').prop('disabled', false); // Enable the date input

      }
   });

});
</script>

@endpush
