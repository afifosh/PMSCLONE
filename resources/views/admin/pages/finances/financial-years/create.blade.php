@if ($financialYear->id)
    {!! Form::model($financialYear, ['route' => ['admin.finances.financial-years.update', $financialYear->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($financialYear, ['route' => ['admin.finances.financial-years.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
    {{-- label --}}
    <div class="form-group col-6">
        {{ Form::label('label', __('Label'), ['class' => 'col-form-label']) }}
        {!! Form::text('label', null, ['class' => 'form-control', 'placeholder' => __('Label')]) !!}
    </div>
    <div class="form-group col-6">
      {{ Form::label('currency', __('Currency'), ['class' => 'col-form-label']) }}
      {!! Form::select('currency', $currency ?? [], $selected_currency ?? null, [
        'data-placeholder' => 'Select Currency',
        'class' => 'form-select globalOfSelect2Remote',
        'data-url' => route('resource-select', ['Currency'])
        ])!!}
    </div>
    {{-- initial balance --}}
    <div class="form-group col-6">
      {{ Form::label('initial_balance', __('Initial Balance'), ['class' => 'col-form-label']) }}
      {!! Form::text('initial_balance', null, ['class' => 'form-control', 'disabled' => $financialYear->id > 0, 'placeholder' => __('Initial Balance')]) !!}
  </div>
    {{-- start Date --}}
    <div class="form-group col-6">
        {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
        {!! Form::text('start_date', null, ['class' => 'form-control flatpickr', 'placeholder' => __('Start Date')]) !!}
    </div>
    {{-- end Date --}}
    <div class="form-group col-6">
        {{ Form::label('end_date', __('End Date'), ['class' => 'col-form-label']) }}
        {!! Form::text('end_date', null, ['class' => 'form-control flatpickr', 'placeholder' => __('End Date')]) !!}
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
