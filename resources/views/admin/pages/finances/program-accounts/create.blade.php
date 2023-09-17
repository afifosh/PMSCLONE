@if ($accountBalance->id)
    {!! Form::model($accountBalance, ['route' => ['admin.finances.program-accounts.update', $accountBalance->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($accountBalance, ['route' => ['admin.finances.program-accounts.store'], 'method' => 'POST']) !!}
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
    {{-- select programs --}}
    <div class="form-group col-12">
        {{ Form::label('holders', __('Holders'), ['class' => 'col-form-label']) }}
        {!! Form::select('holders[]', $programs ?? [], $selected_programs ?? null, [
          'data-placeholder' => 'Select Programs',
          'class' => 'form-select globalOfSelect2Remote',
          'multiple' => 'multiple',
          'data-url' => route('resource-select', ['Program'])
          ])!!}
</div>
<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
        <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
    </div>
</div>
{!! Form::close() !!}
</div>
