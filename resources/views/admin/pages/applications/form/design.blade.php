@extends('admin.layouts/layoutMaster')
@section('title', __('Form'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Design Form') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('admin.dashboard'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item">{!! Html::link(route('admin.applications.settings.forms.index'), __('Forms'), []) !!}</li>
            <li class="breadcrumb-item active"> {{ __('Design Form') }} </li>
        </ul>
    </div>
@endsection
@section('content')
    <div class="row">
      <div class="row">
        <div class="col-md-6 d-flex">
            <h1>Design: </h1>
            {{-- Form Type select --}}
            <div class="form-group pt-2">
                <select class="form-control" id="form-select">
                  <option value="form">Form</option>
                  <option value="wizard">Wizard</option>
                </select>
            </div>
        </div>

        <div class="col-md-6 text-right">
            <!-- This form holds the JSON form definition. -->
            <form method="POST" action="{{ route('admin.applications.settings.forms.design.update', $form) }}">
                @csrf
                @method('PUT')
                <textarea name="definition" id="definition" cols="30" rows="10"></textarea>
                {{-- <input type="hidden" name="definition" id="definition" value=""> --}}

                <button type="submit" class="btn btn-outline-primary" data-form="ajax-form">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    Save Form
                </button>
        </div>
      </div>

      <!-- This becomes the builder. -->
      <div id="formio-builder"></div>
    </div>
@endsection
@push('style')
{{-- <link rel="stylesheet" href="https://cdn.form.io/js/formio.full.min.css"> --}}
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<style>
  .form-builder-panel {
    margin-bottom: 1rem;
  }
  .builder-sidebar_search {
    display: none !important;
  }
</style>
@endpush
{{-- @vite('resources/js/formbuilder.js') --}}
@push('scripts')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
{{-- <script src="https://formio.github.io/formio.js/dist/formio.full.min.js"></script> --}}
<script src="{{ asset('js/formbuilder.js') }}"></script>

{{-- <script src="https://cdn.form.io/js/formio.full.min.js"></script> --}}
{{-- <script src="https://unpkg.com/formiojs@4.0.3/dist/formio.full.min.js"></script> --}}
<script>
var builder = null;
function initBuilder(definition) {
  if (builder) {
    builder.destroy();
    document.getElementById("formio-builder").innerHTML = '';
  }
  $('#form-select').val(definition.display);
  // Formio.Templates.framework = "bootstrap5"
  // Formio.icons = "fontawesome"
  Formio.builder(
          document.getElementById('formio-builder'),
          definition,
          {
          } // these are the opts you can customize
      ).then(function(instance) {
         builder = instance;
          // Exports the JSON representation of the dynamic form to that form we defined above
          document.getElementById('definition').value = JSON.stringify(instance.schema);
          instance.on('change', function (e) {
              // On change, update the above form w/ the latest dynamic form JSON
              document.getElementById('definition').value = JSON.stringify(instance.schema);
          })
      });
}

var definition = @if(isset($definition) && $definition) {!! $definition !!} @else {} @endif;
  definition.display = definition.display || 'form';
initBuilder(definition);

$('#form-select').on('change', function() {
  var display_type = $(this).val();
  definition.display = display_type;
  initBuilder(definition);
});
</script>
@endpush
