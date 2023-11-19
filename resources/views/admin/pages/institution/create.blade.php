@if ($institution->id)
    {!! Form::model($institution, ['route' => ['admin.institutions.update', $institution->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($institution, ['route' => ['admin.institutions.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif
<div class="row">
  {{-- Featured Image --}}
  <div class="card-body mb-3">
    <div class="d-flex align-items-start align-items-sm-center gap-4">
      <img src="{{ $institution->avatar }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
      <div class="button-wrapper">
          @csrf
          <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
            <span class="d-none d-sm-block">Upload new photo</span>
            <i class="ti ti-upload d-block d-sm-none"></i>
          </label>
          <button type="button" class="btn btn-label-secondary institution-image-reset mb-3">
            <i class="ti ti-refresh-dot d-block d-sm-none "></i>
            <span class="d-none d-sm-block">Reset</span>
          </button>
          <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 800K</div>
          <input type="file" id="upload" name="avatar" class="institution-file-input" hidden accept="image/png, image/jpeg" />
          @error('avatar')
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
  <div class="mb-3 col-md-12 mt-3">
    <label for="name" class="form-label">Name</label>
    <input class="form-control" type="text" id="name" name="name" value="{{ old('name') ?? $institution->name }}" autofocus />
    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
  </div>
  <div class="mb-3 col-md-6">
      <label for="email" class="form-label">Email</label>
      <input class="form-control" type="email" id="email" name="email"
          value="{{ old('email') ?? $institution->email }}" />
      @error('email')<div class="text-danger">{{ $message }}</div>@enderror
  </div>
  <div class="mb-3 col-md-6">
      <label for="website" class="form-label">Website</label>
      <input class="form-control" type="text" id="website" name="website"
          value="{{ old('website') ?? $institution->website }}" />
      @error('website')<div class="text-danger">{{ $message }}</div>@enderror
  </div>
  <div class="form-group mb-3 col-md-12">
    {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
    <br>
    <input type="text" value="{{$institution->phone}}" name="phone" class='form-control ignore-ajax-error w-100', placeholder={{__('Phone')}}, id='phone'>
    <span id="itiPhone"></span>
    <input type="hidden" id="itiPhoneCountry" class="ignore-ajax-error" name="phone_country">
  </div>

  <div class="form-group mb-3 col-12">
    {{ Form::label('address', __('Address'), ['class' => 'col-form-label']) }}
    {!! Form::textarea('address', $institution->address , ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Address')]) !!}
  </div>

  <div class="mb-3 col-6">
    {{ Form::label('country_id', __('Country'), ['class' => 'form-label']) }}
    {!! Form::select('country_id', $countries, $institution->country_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['Country']),
    'id' => 'user-countries-id',
    ]) !!}
  </div>
  {{-- states --}}
  <div class="mb-3 col-6">
    {{ Form::label('state_id', __('State'), ['class' => 'form-label']) }}
    {!! Form::select('state_id', $states, $institution->state_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['State']),
    'data-dependent_id' => 'user-countries-id',
    'id' => 'user-state-id',
    ]) !!}
  </div>
  {{-- cities --}}
  <div class="mb-3 col-6">
    {{ Form::label('city_id', __('City'), ['class' => 'form-label']) }}
    {!! Form::select('city_id', $cities, $institution->city_id, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['City']),
    'data-dependent_id' => 'user-state-id'
    ]) !!}
  </div>
  <div class="mb-3 col-md-6">
    <label for="zipCode" class="form-label">Zip Code</label>
    <input type="text" class="form-control" id="zipCode" name="zip_code" value="{{ old('zip_code') ?? $institution->zip_code }}" placeholder="231465" maxlength="6" />
    @error('zip_code')<div class="text-danger">{{ $message }}</div>@enderror
  </div>  
  <div class="mb-3 col-md-4">
    <label for="language" class="form-label">Language</label>
    <select id="language" name="language" class="select2 form-select">
      <option value="">Select Language</option>
      @forelse ($languages as $key => $lang)
          <option value="{{ $key }}" {{old('language') == $key || $key == $institution->language ? 'selected' : '' }}>{{ $lang }}</option>
      @empty
      @endforelse
    </select>
    @error('language')<div class="text-danger">{{ $message }}</div>@enderror
  </div>
  <div class="mb-3 col-md-4">
    <label for="timeZones" class="form-label">Timezone</label>
    <select id="timeZones" name="timezone" class="select2 form-select">
      <option value="">Select Timezone</option>
      @forelse ($timezones as $tz)
          <option value="{{ $tz['value'] }}" {{ $tz['value'] == old('timezone') || $tz['value'] == $institution->timezone ? 'selected' : ''}}>{{ $tz['label'] }}</option>
      @empty
      @endforelse
    </select>
    @error('timezone')<div class="text-danger">{{ $message }}</div>@enderror
  </div>
  <div class="mb-3 col-md-4">
    <label for="currency" class="form-label">Currency</label>
    <select id="currency" name="currency" class="select2 form-select">
      <option value="">Select Currency</option>
      @forelse ($currencies as $key => $cur)
          <option value="{{ $key }}" {{old('currency') == $key || $key == $institution->currency ? 'selected' : '' }}>{{ $cur }}</option>
      @empty
      @endforelse
    </select>
    @error('currency')<div class="text-danger">{{ $message }}</div>@enderror
  </div>
</div>

  {{-- Status --}}
  <div class="form-group col-12">
    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
    {!! Form::select('status', ['Active' => 'Active', 'Inactive' => 'Inactive'], $institution->status, [
    'class' => 'form-select globalOfSelect2',
    ]) !!}
  </div>
<div class="mt-3 d-flex justify-content-end">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
  </div>
{!! Form::close() !!}
<script>
  (function () {
    // Update/reset user image of account page
    let institutionImage = document.getElementById('uploadedAvatar');
    const fileInput = document.querySelector('.institution-file-input'),
          resetFileInput = document.querySelector('.institution-image-reset');
  
    let currentArtworkImage = institutionImage.src; // Store the initial or last uploaded image URL
  
    fileInput.onchange = () => {
      if (fileInput.files[0]) {
        lastUploadedImage = window.URL.createObjectURL(fileInput.files[0]); // Update with new uploaded image URL
        institutionImage.src = lastUploadedImage;
      }
    };
  
    resetFileInput.onclick = () => {
      fileInput.value = '';
      institutionImage.src = currentArtworkImage || 'https://preview.keenthemes.com/metronic8/demo1/assets/media/svg/avatars/blank.svg'; // Reset to last uploaded image or default
    };
  })();
  
  </script>
