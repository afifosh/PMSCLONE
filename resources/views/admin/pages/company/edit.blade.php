@if ($company->id)
    {!! Form::model($company, ['route' => ['admin.companies.update', $company->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($company, ['route' => ['admin.companies.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif
<div class="row">

    <div class="form-group  col-12">
        {{-- {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }} --}}
        <div class="row">
          <div class="col-md mb-md-0 mb-3">
            <div class="form-check custom-option custom-option-icon {{!$company->type || $company->type == 'Company' ? 'checked': ''}}">
              <label class="form-check-label custom-option-content" for="customRadioCompany">
                <span class="custom-option-body">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-skyscraper" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 21l18 0"></path>
                        <path d="M5 21v-14l8 -4v18"></path>
                        <path d="M19 21v-10l-6 -4"></path>
                        <path d="M9 9l0 .01"></path>
                        <path d="M9 12l0 .01"></path>
                        <path d="M9 15l0 .01"></path>
                        <path d="M9 18l0 .01"></path>
                     </svg>
                  <span class="custom-option-title">Company</span>
                </span>
                {!! Form::radio('type', 'Company', false, ['class' => 'form-check-input', 'id' => 'customRadioCompany', 'checked']) !!}
              </label>
            </div>
          </div>
          <div class="col-md mb-md-0 mb-3">
            <div class="form-check custom-option custom-option-icon {{$company->type == 'Person' ? 'checked': ''}}">
              <label class="form-check-label custom-option-content" for="customRadioPerson">
                <span class="custom-option-body">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                     </svg>
                  <span class="custom-option-title"> Person </span>
                </span>
                {!! Form::radio('type', 'Person', false, ['class' => 'form-check-input', 'id' => 'customRadioPerson']) !!}
              </label>
            </div>
          </div>
        </div>
        <small class="form-text text-muted">{{ __('Select the type: Company or Person') }}</small>
      </div>
    <div class="form-group col-6">
        {{ Form::label('name', __('English Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', $company->getRawOriginal('name') ? $company->getRawOriginal('name') : '', ['class' => 'form-control', 'placeholder' => __('English Name')]) !!}
    </div>

    {{-- name_ar --}}
    <div class="form-group col-6">
      {{ Form::label('name_ar', __('Arabic Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name_ar', null, ['class' => 'form-control', 'placeholder' => __('Arabic Name')]) !!}
    </div>

    <div class="form-group col-12">
        {{ Form::label('address', __('Address'), ['class' => 'col-form-label']) }}
        {!! Form::textarea('address', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Address')]) !!}
    </div>

    {{-- country --}}
    <div class="form-group col-6">
      {{ Form::label('country_id', __('Country'), ['class' => 'col-form-label']) }}
      {!! Form::select('country_id', $countries, null, [
      'class' => 'form-select globalOfSelect2Remote',
      'data-url' => route('resource-select', ['Country']),
      'id' => 'user-countries-id',
      ]) !!}
    </div>
    {{-- states --}}
    <div class="form-group col-6">
      {{ Form::label('state_id', __('State'), ['class' => 'col-form-label']) }}
      {!! Form::select('state_id', $states, null, [
      'class' => 'form-select globalOfSelect2Remote',
      'data-url' => route('resource-select', ['State']),
      'data-dependent_id' => 'user-countries-id',
      'id' => 'user-state-id',
      ]) !!}
    </div>
    {{-- cities --}}
    <div class="form-group col-6">
      {{ Form::label('city_id', __('City'), ['class' => 'col-form-label']) }}
      {!! Form::select('city_id', $cities, null, [
      'class' => 'form-select globalOfSelect2Remote',
      'data-url' => route('resource-select', ['City']),
      'data-dependent_id' => 'user-state-id'
      ]) !!}
    </div>

    <div class="form-group col-6">
        {{ Form::label('zip', __('Zip'), ['class' => 'col-form-label']) }}
        {!! Form::text('zip', null, ['class' => 'form-control', 'placeholder' => __('Zip')]) !!}
    </div>

    <div class="form-group col-6">
        {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
        <br>
        <input type="text" value="{{$company->phone}}" name="phone" class='form-control ignore-ajax-error w-100', placeholder={{__('Phone')}}, id='phone'>
        <span id="itiPhone"></span>
        <input type="hidden" id="itiPhoneCountry" class="ignore-ajax-error" name="phone_country">
    </div>

    <div class="form-group col-6">
        {{ Form::label('email', __('Email Address'), ['class' => 'col-form-label']) }}
        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Email Address')]) !!}
    </div>

    <div class="form-group col-12">
        {{ Form::label('website', __('Website'), ['class' => 'col-form-label']) }}
        {!! Form::url('website', null, ['class' => 'form-control', 'placeholder' => __('Website')]) !!}
    </div>

    <div class="form-group col-6">
        {{ Form::label('vat_number', __('VAT Number'), ['class' => 'col-form-label']) }}
        {!! Form::text('vat_number', null, ['class' => 'form-control', 'placeholder' => __('VAT Number')]) !!}
    </div>

    <div class="form-group col-6">
        {{ Form::label('gst_number', __('GST Number'), ['class' => 'col-form-label']) }}
        {!! Form::text('gst_number', null, ['class' => 'form-control', 'placeholder' => __('GST Number')]) !!}
    </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
<script>
  $(document).on('change', '[name="type"]', function(){
    $(this).parents('.form-check').addClass('checked');
    $(this).parents('.form-check').parent().siblings().find('.form-check').removeClass('checked');
  })
</script>
