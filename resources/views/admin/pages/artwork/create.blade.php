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
            <input type="file" id="upload" name="featured_image" class="artwork-file-input" hidden accept="image/png, image/jpeg" />
          </label>
          <button type="button" class="btn btn-label-secondary artwork-image-reset mb-3">
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
    {!! Form::select('medium_id', $mediums ?? ['' => 'Select Medium'], $artwork->medium_id, ['class' => 'form-select globalOfSelect2Remote', 'data-url' => route('resource-select', ['Medium']),  'data-allow-clear' => 'true', 'data-tags' => 'true', 'data-placeholder' => __('Select Medium'), 'id' => 'artwork-mediums-id']) !!}
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
(function () {
  // Update/reset user image of account page
  let artworkImage = document.getElementById('uploadedAvatar');
  const fileInput = document.querySelector('.artwork-file-input'),
        resetFileInput = document.querySelector('.artwork-image-reset');
  
  let currentArtworkImage = artworkImage.src; // Store the initial or last uploaded image URL

  fileInput.onchange = () => {
    if (fileInput.files[0]) {
      lastUploadedImage = window.URL.createObjectURL(fileInput.files[0]); // Update with new uploaded image URL
      artworkImage.src = lastUploadedImage;
    }
  };

  resetFileInput.onclick = () => {
    fileInput.value = '';
    artworkImage.src = currentArtworkImage || 'https://preview.keenthemes.com/metronic8/demo1/assets/media/svg/avatars/blank.svg'; // Reset to last uploaded image or default
  };
})();

</script>