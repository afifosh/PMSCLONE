@if ($application->id)
  {!! Form::model($application, ['route' => ['admin.applications.update', ['application' => $application]],
      'method' => 'PUT'
  ]) !!}
@else
  {!! Form::model($application, ['route' => ['admin.applications.store'], 'method' => 'POST']) !!}
@endif
<div class="row">
  {{-- name --}}
  <div class="form-group col-6">
      {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
  </div>

  {{-- select program --}}
  <div class="form-group col-6">
    {{ Form::label('program_id', __('Program'), ['class' => 'col-form-label']) }}
    {!! Form::select('program_id', $programs ?? [], $selectedProgram ?? null, [
    'data-placeholder' => __('Select Program'),
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Program']),
    'data-allow-clear' => 'true'
    ])!!}
  </div>

  {{-- select type --}}
  <div class="form-group col-6">
    {{ Form::label('type_id', __('Type'), ['class' => 'col-form-label']) }}
    {!! Form::select('type_id', $types ?? [], $selectedType ?? null, [
    'data-placeholder' => __('Select Type'),
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['ApplicationType']),
    'data-allow-clear' => 'true'
    ])!!}
  </div>

  {{-- select category --}}
  <div class="form-group col-6">
    {{ Form::label('category_id', __('Category'), ['class' => 'col-form-label']) }}
    {!! Form::select('category_id', $categories ?? [], $selectedCategory ?? null, [
    'data-placeholder' => __('Select Category'),
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['ApplicationCategory']),
    'data-allow-clear' => 'true'
    ])!!}
  </div>

  {{-- select pipeline --}}
  <div class="form-group col-6">
    {{ Form::label('pipeline_id', __('Pipeline'), ['class' => 'col-form-label']) }}
    {!! Form::select('pipeline_id', $pipelines ?? [], $selectedPipeline ?? null, [
    'data-placeholder' => __('Select Pipeline'),
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['ApplicationPipeline']),
    'data-allow-clear' => 'true'
    ])!!}
  </div>

  {{-- select score_card --}}
  <div class="form-group col-6">
    {{ Form::label('score_card_id', __('Score Card'), ['class' => 'col-form-label']) }}
    {!! Form::select('scorecard_id', $scoreCards ?? [], $selectedScoreCard ?? null, [
    'data-placeholder' => __('Select Score Card'),
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['ApplicationScoreCard']),
    'data-allow-clear' => 'true'
    ])!!}
  </div>

  {{-- select Form --}}
  <div class="form-group col-6">
    {{ Form::label('form_id', __('Form'), ['class' => 'col-form-label']) }}
    {!! Form::select('form_id', $forms ?? [], $selectedForm ?? null, [
    'data-placeholder' => __('Select Form'),
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['ApplicationForm']),
    'data-allow-clear' => 'true'
    ])!!}
  </div>

  {{-- start_at --}}
  <div class="form-group col-6">
      {{ Form::label('start_at', __('Start At'), ['class' => 'col-form-label']) }}
      {!! Form::text('start_at', null, ['class' => 'form-control flatpickr', 'placeholder' => __('Start At')]) !!}
  </div>

  {{-- end_at --}}
  <div class="form-group col-6">
      {{ Form::label('end_at', __('End At'), ['class' => 'col-form-label']) }}
      {!! Form::text('end_at', null, ['class' => 'form-control flatpickr', 'placeholder' => __('End At')]) !!}
  </div>

  {{-- is_public --}}
  <div class="col-6 mt-4">
    <label class="switch mt-3">
      {{ Form::checkbox('is_public', 1, !$application->company_id,['class' => 'switch-input'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Is public?</span>
    </label>
  </div>

  {{-- select company --}}
  <div class="form-group col-12 {{$application->company_id ?: 'd-none'}}">
    {{ Form::label('company_id', __('Client'), ['class' => 'col-form-label']) }}
    {!! Form::select('company_id', $companies ?? [], $selectedCompany ?? null, [
    'data-placeholder' => __('Select Company'),
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['groupedCompany'])
    ])!!}
  </div>

  {{-- select users --}}
  <div class="form-group col-12">
    {{ Form::label('application_users[]', __('Users'), ['class' => 'col-form-label']) }}
    {!! Form::select('application_users[]', $users ?? [], $selectedUsers ?? null, [
    'data-placeholder' => __('Select Users'),
    'class' => 'form-select globalOfSelect2UserRemote',
    'data-url' => route('resource-select-user', ['Admin']),
    'multiple',
    'data-allow-clear' => 'true'
    ])!!}
  </div>

  {{-- description --}}
  <div class="form-group col-12">
      {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
      {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5, 'placeholder' => __('Description')]) !!}
  </div>
</div>
<div class="mt-3 d-flex justify-content-end">
  <div class="btn-flt float-end">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
  </div>
</div>
{!! Form::close() !!}

<script>
  // on change of is_public, show/hide company_id
  $(document).on('change', 'input[name="is_public"]', function() {
    if ($(this).is(':checked')) {
      $('select[name="company_id"]').closest('.form-group').addClass('d-none');
    } else {
      $('select[name="company_id"]').closest('.form-group').removeClass('d-none');
    }
  });
</script>
