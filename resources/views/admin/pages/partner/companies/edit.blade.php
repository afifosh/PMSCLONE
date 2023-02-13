@if ($company->id)
    {!! Form::model($company, ['route' => ['admin.partner.companies.update', $company->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
@else
    {!! Form::model($company, ['route' => ['admin.partner.companies.store'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
@endif

<div class="row">
    <div class="form-group col-6">
        {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
    </div>

    <div class="form-group col-6">
        {{ Form::label('website', __('Website'), ['class' => 'col-form-label']) }}
        {!! Form::text('website', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Website')]) !!}
    </div>

    <div class="form-group">
      {{ Form::label('phone', __('Phone'), ['class' => 'col-form-label']) }}
      {!! Form::text('phone', null, ['class' => 'form-control', 'required'=> 'true', 'placeholder' => __('Phone Number')]) !!}
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
