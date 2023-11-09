@if ($artwork->id)
  {!! Form::model($artwork, ['route' => ['admin.artworks.update', ['artwork' => $artwork]],
      'method' => 'PUT',
      'id' => 'stage-create-form',
  ]) !!}
@else
  {!! Form::model($artwork, ['route' => ['admin.artworks.store'], 'method' => 'POST', 'id' => 'stage-create-form',]) !!}
@endif
<div class="row">
  {{-- title --}}
  <div class="form-group col-6">
    {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
    {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Title')]) }}
  </div>
  {{-- medium --}}
  <div class="form-group col-6">
    {{ Form::label('medium_id', __('Medium'), ['class' => 'col-form-label']) }}
    {!! Form::select('medium_id', $mediums ?? [], $artwork->medium_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Medium']),
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select Medium'),
    'id' => 'artwork-mediums-id',
    ]) !!}
  </div>
  {{-- program --}}
  <div class="form-group col-6 {{$artwork->year ? 'd-none' : ''}}">
    {{ Form::label('program_id', __('Program'), ['class' => 'col-form-label']) }}
    {!! Form::select('program_id', $programs ?? [], null, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Program']),
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select Program'),
    'id' => 'artwork-programs-id',
    ]) !!}
  </div>
  {{-- year --}}
  <div class="form-group col-6 {{$artwork->program_id ? 'd-none' : ''}}">
    {{ Form::label('year', __('Year'), ['class' => 'col-form-label']) }}
    {{ Form::text('year', null, ['class' => 'form-control', 'placeholder' => __('Year')]) }}
  </div>
  {{-- dimension --}}
  <div class="form-group col-6">
    {{ Form::label('dimension', __('Dimension'), ['class' => 'col-form-label']) }}
    {{ Form::text('dimension', null, ['class' => 'form-control', 'placeholder' => __('Dimension')]) }}
  </div>
  {{-- Featured_image --}}
  <div class="form-group col-6">
    {{ Form::label('featured_image', __('Featured Image'), ['class' => 'col-form-label']) }}
    {{ Form::file('featured_image', ['class' => 'form-control', 'placeholder' => __('Featured Image')]) }}
  </div>
  {{-- location --}}
  <div class="form-group col-6">
    {{ Form::label('location_id', __('Location'), ['class' => 'col-form-label']) }}
    {!! Form::select('location_id', $locations ?? [], null, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Location']),
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select Location'),
    'id' => 'artwork-locations-id',
    ]) !!}
  </div>
  {{-- Contract --}}
  <div class="form-group col-6">
    {{ Form::label('contract_id', __('Placed By Contract'), ['class' => 'col-form-label']) }}
    {!! Form::select('contract_id', $contracts ?? [], $artwork->contract_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Contract']),
    'data-allow-clear' => 'true',
    'data-placeholder' => __('Select Contract'),
    'id' => 'artwork-contracts-id',
    ]) !!}
  </div>
  {{-- is_temporary_location --}}
  <div class="col-6 mt-4">
    <label class="switch mt-3">
      {{ Form::checkbox('is_temporary_location', 1, $artwork->latestLocation->added_till ?? false && 1,['class' => 'switch-input'])}}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Is Temporary Location?</span>
    </label>
  </div>
  {{-- added till --}}
  <div class="form-group col-6 {{($artwork->latestLocation->added_till ?? false) ? '' : 'd-none'}}">
    {{ Form::label('added_till', __('Added Till'), ['class' => 'col-form-label']) }}
    {{ Form::date('added_till', $artwork->latestLocation->added_till ?? null, ['class' => 'form-control flatpickr', 'placeholder' => __('Added Till')]) }}
  </div>

  {{-- description --}}
  <div class="form-group col-12">
    {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
    {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Description'), 'rows' => 5]) }}
  </div>
  <div class="mt-3 d-flex justify-content-end">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
  </div>
{!! Form::close() !!}

<script>
  //if program_id selected, hide year
  $(document).on('change', '#artwork-programs-id', function(){
    console.log($(this).val());
    if($(this).val()){
      $('#stage-create-form [name="year"]').parent().addClass('d-none');
    }else{
      $('#stage-create-form [name="year"]').parent().removeClass('d-none');
    }
  })

  // if temporary location selected, show added till
  $(document).on('change', '#stage-create-form [name="is_temporary_location"]', function(){
    if($(this).is(':checked')){
      $('#stage-create-form [name="added_till"]').parent().removeClass('d-none');
    }else{
      $('#stage-create-form [name="added_till"]').parent().addClass('d-none');
    }
  })
</script>
