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
            <div class="form-check custom-option custom-option-icon">
              <label class="form-check-label custom-option-content" for="customRadioHome">
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
                {!! Form::radio('type', 'Company', false, ['class' => 'form-check-input']) !!} 

              </label>
            </div>
          </div>
          <div class="col-md mb-md-0 mb-3">
            <div class="form-check custom-option custom-option-icon">
              <label class="form-check-label custom-option-content" for="customRadioOffice">
                <span class="custom-option-body">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                     </svg>

                  <span class="custom-option-title"> Person </span>
                </span>
                {!! Form::radio('type', 'Person', false, ['class' => 'form-check-input']) !!} 
              </label>
            </div>
          </div>
        </div>
        <small class="form-text text-muted">{{ __('Select the type: Company or Person') }}</small>
        <span class="text-danger">{{ $errors->first('type') }}</span>
      </div>
    <div class="form-group col-12">
        {{ Form::label('name', __('Company Name/Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Company Name')]) !!}
    </div>

    <div class="form-group col-12">
        {{ Form::label('address', __('Address'), ['class' => 'col-form-label']) }}
        {!! Form::textarea('address', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Address')]) !!}
    </div>
    
    <div class="form-group col-6">
        {{ Form::label('city', __('City'), ['class' => 'col-form-label']) }}
        {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('City')]) !!}
    </div>
    
    <div class="form-group col-6">
        {{ Form::label('state', __('State'), ['class' => 'col-form-label']) }}
        {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('State')]) !!}
    </div>
    
    <div class="form-group col-6">
        {{ Form::label('zip', __('Zip'), ['class' => 'col-form-label']) }}
        {!! Form::text('zip', null, ['class' => 'form-control', 'placeholder' => __('Zip')]) !!}
    </div>
    
    <div class="form-group col-6">
        {{ Form::label('country', __('Country'), ['class' => 'col-form-label']) }}
        {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' => __('Country')]) !!}
    </div>
    
    <div class="form-group col-6">
        {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
        {!! Form::tel('phone', null, ['class' => 'form-control', 'placeholder' => __('Phone')]) !!}
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
{{-- 
    <div class="form-group col-6">
    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
    @php
      $enum = $company::getPossibleEnumValues('status');
    @endphp
        {!! Form::select('status', array_combine($enum, array_map('ucwords',$enum)), $company->status, ['class' => 'form-select globalOfSelect2']) !!}
    </div> --}}
  {{-- <div class="form-group">

    {{ Form::label('avatar', __('Avatar'), ['class' => '']) }}

    {!! Form::file('avatar', ['class' => 'form-control', 'accept'=> 'image/*']) !!}
</div> --}}
</div>
{{-- <div class="form-group">

    {{ Form::label('roles', __('Role'), ['class' => 'col-form-label']) }}


    {!! Form::select('roles[]', $roles, $user->roles, ['class' => 'form-select globalOfSelect2', 'multiple' => 'multiple']) !!}
</div> --}}


<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
