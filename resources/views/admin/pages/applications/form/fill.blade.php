@php
    use App\Facades\UtilityFacades;
@endphp
@extends('admin.layouts/layoutMaster')
@section('title', __('Form Fill'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Form Fill') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('admin.dashboard'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item">{!! Html::link(route('admin.applications.settings.forms.index'), __('Forms'), []) !!}</li>
            <li class="breadcrumb-item active"> {{ __('Form Fill') }} </li>
        </ul>
    </div>
@endsection
@section('content')
    {{-- @include('admin.pages.applications.form.multi-form') --}}
<div class="row">
  <div class="col-md-6 text-right">
      <!-- This form holds the values the user has entered, as a JSON document. -->
      <form method="post" id="submissionForm" action="{{ route('admin.applications.settings.forms.fill.store', $form) }}">
          @csrf
          @method('PUT')
          <!-- State can be used to capture a Submit vs. Save Draft button -->
          <input type="hidden" name="state">
          <!-- The JSON with all the values -->
          <input type="hidden" name="submissionValues" id="submissionValues" value="">
          <button type="submit" id="submissionFormSubmit" class="btn btn-outline-primary d-none" data-form="ajax-form">
              <i class="fas fa-save" aria-hidden="true"></i>
              Save Form
          </button>
      </form>
  </div>
</div>

<!-- Any server-side errors will be shown here. This is a fallback for when the client-side validations miss something. -->
@if ($errors->any())
<div class="alert alert-danger">
  <p style="font-size: 16pt"><strong>Oops</strong>, there was an issue with that.</p>
  <ul class="ml-5">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
  </ul>
</div>
@endif

<!-- This becomes the builder. -->
<div id="formio-form"></div>
@endsection
@push('style')
<link rel="stylesheet" href="https://cdn.form.io/js/formio.full.min.css">
@endpush
@push('scripts')
    <script src="{{ asset('js/formbuilder.js') }}"></script>
    <script lang="text/javascript">
      window.onload = function() {
          Formio.createForm(document.getElementById('formio-form'), {!! $form->json !!}).then(function (form) {
              form.submission = {
                  data: {!! $data !!},
              };

              form.on('submit', function (submission) {
                  var submitForm = document.getElementById('submissionForm');
                  submitForm.querySelector('input[name=state]').value = submission.state;
                  submitForm.querySelector('input[name=submissionValues]').value = JSON.stringify(submission.data);

                  $('#submissionFormSubmit').click();
              });
          });
      };
  </script>
@endpush
