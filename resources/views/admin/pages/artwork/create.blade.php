@if ($artwork->id)
  {!! Form::model($artwork, ['route' => ['admin.artworks.update', $artwork->id], 'method' => 'PUT', 'id' => 'stage-create-form']) !!}
@else
  {!! Form::model($artwork, ['route' => ['admin.artworks.store'], 'method' => 'POST', 'id' => 'stage-create-form']) !!}
@endif

<div class="row">
  {{-- Title --}}
  <div class="form-group col-6">
    {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
    {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Title')]) }}
  </div>

  {{-- Medium --}}
  <div class="form-group col-6">
    {{ Form::label('medium_id', __('Medium'), ['class' => 'col-form-label']) }}
    {!! Form::select('medium_id', $mediums ?? [], $artwork->medium_id, ['class' => 'form-select globalOfSelect2Remote', 'data-url' => route('resource-select', ['Medium']), 'data-allow-clear' => 'true', 'data-placeholder' => __('Select Medium'), 'id' => 'artwork-mediums-id']) !!}
  </div>

  {{-- Program --}}
  <div class="form-group col-6">
    {{ Form::label('program_id', __('Program'), ['class' => 'col-form-label']) }}
    {!! Form::select('program_id', $programs ?? [], null, ['class' => 'form-select globalOfSelect2Remote', 'data-url' => route('resource-select', ['Program']), 'data-allow-clear' => 'true', 'data-placeholder' => __('Select Program'), 'id' => 'artwork-programs-id']) !!}
  </div>

  {{-- Year --}}
  <div class="form-group col-6">
    {{ Form::label('year', __('Year'), ['class' => 'col-form-label']) }}
    {{ Form::text('year', null, ['class' => 'form-control', 'placeholder' => __('Year')]) }}
  </div>

  {{-- Description --}}
  <div class="form-group col-12">
    {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
    {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Description'), 'rows' => 5]) }}
  </div>

  {{-- Weight --}}
  <div class="form-group col-6">
    {{ Form::label('weight', __('Weight'), ['class' => 'col-form-label']) }}
    <div class="input-group form-group">
      {{ Form::number('weight', null, ['class' => 'form-control', 'placeholder' => __('Weight'), 'step' => '0.01']) }}
      {!! Form::select('weight_unit', $weight_unit, $artwork->weight_unit, ['class' => 'form-select', 'id' => 'weight-unit-id']) !!}
    </div>
  </div>

  
  {{-- Width --}}
  <div class="form-group col-6">
    {{ Form::label('width_unit', __('Width Unit'), ['class' => 'col-form-label']) }}
    <div class="input-group form-group">
      {{ Form::number('width', null, ['class' => 'form-control', 'placeholder' => __('Width'), 'step' => '0.01']) }}
      {!! Form::select('width_unit', $width_unit, $artwork->width_unit, ['class' => 'form-select', 'id' => 'width-unit-id']) !!}
    </div>
  </div>

  {{-- Height --}}
  <div class="form-group col-6">
    {{ Form::label('height', __('Height'), ['class' => 'col-form-label']) }}
    <div class="input-group form-group">
      {{ Form::number('height', null, ['class' => 'form-control', 'placeholder' => __('Height'), 'step' => '0.01']) }}
      {!! Form::select('height_unit', $height_unit, $artwork->height_unit, ['class' => 'form-select', 'id' => 'height-unit-id']) !!}
    </div>
  </div>

  {{-- Depth --}}
  <div class="form-group col-6">
    {{ Form::label('depth', __('Depth'), ['class' => 'col-form-label']) }}
    <div class="input-group form-group">
      {{ Form::number('depth', null, ['class' => 'form-control', 'placeholder' => __('Depth'), 'step' => '0.01']) }}
      {!! Form::select('depth_unit', $depth_unit, $artwork->depth_unit, ['class' => 'form-select', 'id' => 'depth-unit-id']) !!}
    </div>
  </div>


  {{-- Is Sold Checkbox --}}
  <div class="col-6 mt-4">
    <label class="switch mt-3">
      {{ Form::checkbox('is_sold', 1, $artwork->is_sold, ['class' => 'switch-input']) }}
      <span class="switch-toggle-slider">
        <span class="switch-on"></span>
        <span class="switch-off"></span>
      </span>
      <span class="switch-label">Is Sold?</span>
    </label>
  </div>

  {{-- Save and Close Buttons --}}
  <div class="mt-3 d-flex justify-content-end">
    <div class="btn-flt float-end">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
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
