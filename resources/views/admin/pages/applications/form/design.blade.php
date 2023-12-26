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
        <div class="col-md-6">
            <h1>Design Form</h1>
        </div>

        <div class="col-md-6 text-right">
            <!-- This form holds the JSON form definition. -->
            <form method="POST" action="{{ route('admin.applications.settings.forms.design.update', $form) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="definition" id="definition" value="">

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
<link rel="stylesheet" href="https://cdn.form.io/js/formio.full.min.css">
{{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
<style>
  .form-builder-panel {
    margin-bottom: 1rem;
  }
</style>
@endpush
@push('scripts')
<script src="{{ asset('js/formbuilder.js') }}"></script>
{{-- <script src="https://cdn.form.io/js/formio.full.min.js"></script> --}}
<script>
Formio.builder(
          document.getElementById('formio-builder'),
          @if(isset($definition) && $definition) {!! $definition !!} @else {} @endif,
          {} // these are the opts you can customize
      ).then(function(builder) {
          // Exports the JSON representation of the dynamic form to that form we defined above
          document.getElementById('definition').value = JSON.stringify(builder.schema);

          builder.on('change', function (e) {
              // On change, update the above form w/ the latest dynamic form JSON
              document.getElementById('definition').value = JSON.stringify(builder.schema);
          })
      });
</script>
@endpush
