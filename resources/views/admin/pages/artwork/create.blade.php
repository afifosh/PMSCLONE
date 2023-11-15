@if ($artwork->id)
  {!! Form::model($artwork, ['route' => ['admin.artworks.update', $artwork->id], 'method' => 'PUT', 'id' => 'stage-create-form']) !!}
@else
  {!! Form::model($artwork, ['route' => ['admin.artworks.store'], 'method' => 'POST', 'id' => 'stage-create-form']) !!}
@endif

<div class="row">
  {{-- Featured Image --}}
  <div class="card-body mb-3">
    <div class="d-flex align-items-start align-items-sm-center gap-4">
      <img src="{{ $artwork->featured_image }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
      <div class="button-wrapper">
          @csrf
          <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
            <span class="d-none d-sm-block">Upload new photo</span>
            <i class="ti ti-upload d-block d-sm-none"></i>
            <input type="file" id="upload" name="featured_image" class="account-file-input" hidden accept="image/png, image/jpeg" />
          </label>
          <button type="button" class="btn btn-label-secondary account-image-reset mb-3">
            <i class="ti ti-refresh-dot d-block d-sm-none "></i>
            <span class="d-none d-sm-block">Reset</span>
          </button>
          <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 800K</div>
          @error('featured_image')
            <div class="alert alert-danger alert-dismissible my-2">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                {{ $message }}
            </div>
          @enderror
      </div>
    </div>
  </div>
  <hr class="my-0">  
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

  {{-- Weight --}}
  <div class="form-group col-6">
    {{ Form::label('weight', __('Weight'), ['class' => 'col-form-label']) }}
    <div class="input-group form-group">
      {{ Form::number('weight', null, ['class' => 'form-control w-75', 'placeholder' => __('Weight'), 'step' => '0.01']) }}
      {!! Form::select('weight_unit', $weight_unit, $artwork->weight_unit, ['class' => 'form-select w-25', 'id' => 'weight-unit-id']) !!}
    </div>
  </div>

  
  {{-- Width --}}
  <div class="form-group col-6">
    {{ Form::label('width', __('Width'), ['class' => 'col-form-label']) }}
    <div class="input-group form-group">
      {{ Form::number('width', null, ['class' => 'form-control w-75', 'placeholder' => __('Width'), 'step' => '0.01']) }}
      {!! Form::select('width_unit', $width_unit, $artwork->width_unit, ['class' => 'form-select w-25', 'id' => 'width-unit-id']) !!}
    </div>
  </div>

  {{-- Height --}}
  <div class="form-group col-6">
    {{ Form::label('height', __('Height'), ['class' => 'col-form-label']) }}
    <div class="input-group form-group">
      {{ Form::number('height', null, ['class' => 'form-control w-75', 'placeholder' => __('Height'), 'step' => '0.01']) }}
      {!! Form::select('height_unit', $height_unit, $artwork->height_unit, ['class' => 'form-select w-25', 'id' => 'height-unit-id']) !!}
    </div>
  </div>

  {{-- Depth --}}
  <div class="form-group col-6">
    {{ Form::label('depth', __('Depth'), ['class' => 'col-form-label']) }}
    <div class="input-group form-group">
      {{ Form::number('depth', null, ['class' => 'form-control w-75', 'placeholder' => __('Depth'), 'step' => '0.01']) }}
      {!! Form::select('depth_unit', $depth_unit, $artwork->depth_unit, ['class' => 'form-select w-25', 'id' => 'depth-unit-id']) !!}
    </div>
  </div>

  {{-- Description --}}
  <div class="form-group col-12">
    {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
    {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Description'), 'rows' => 4]) }}
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



// // Handle the 'select2:selecting' event
// $('#artwork-mediums-id').on('select2:selecting', function (e) {
//   let selectedOption = e.params.args.data;
//   alert('select event');

//   // Check if the option to add a new tag was selected
//   if (selectedOption.isTag) {
//     e.preventDefault(); // Prevent the default behavior of adding the tag
//     e.stopPropagation(); // Stop the event from propagating further
//     // Trigger the custom 'select2:add-new' event
//     $(e.currentTarget).trigger('select2:add-new', selectedOption);
//   }
// });


// // Event handler for when a selection is made
// $('#artwork-mediums-id').on('select2:select', function () {
//   console.log('select event');
//   alert('select event');

//   e.preventDefault(); // Prevent the default selection behavior
//   // var data = e.params.data;
//   // // Use the jQuery data method to check for the 'select2-tag' data attribute
//   // if ($(data.element).data('select2-tag')) {
//   //   e.preventDefault(); // Prevent the default selection behavior
//   //   // Your logic to handle the 'Add' selection

//   //   $.ajax({
//   //               url: "/test",
//   //               method: "post",
//   //               dataType: "json",
//   //               data: {
//   //                   name: $(this).find("option:selected").val(),
//   //               },
//   //               success: function(response) {
//   //                   // alert(response.status);
//   //                   if (response.status) {
//   //                       $(this).find("option:selected").val(response.data.id);
//   //                       $(this).find("option:selected").text(response.data.name);
//   //                   }
//   //               }
//   //           })
//   //   console.log('Custom logic for adding the new tag.');
//   // } else {
//   //   // Logic for handling other selections
//   // }
// });


</script>
