{!! Form::model($company, ['route' => ['admin.companies.names.store', ['company' => $company->id]], 'method' => 'POST',]) !!}

<div class="row">
    <div class="form-group col-6">
        {{ Form::label('name', __('English Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', $company->getRawOriginal('name') ? $company->getRawOriginal('name') : '', [
          'class' => 'form-control',
          'placeholder' => __('English Name')
        ]) !!}
    </div>

    {{-- name_ar --}}
    <div class="form-group col-6">
      {{ Form::label('name_ar', __('Arabic Name'), ['class' => 'col-form-label']) }}
      {!! Form::text('name_ar', null, [
        'class' => 'form-control',
        'placeholder' => __('Arabic Name')
      ]) !!}
    </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
