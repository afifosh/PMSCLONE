@if ($tax->id)
    {!! Form::model($tax, ['route' => ['admin.finances.taxes.update', $tax->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($tax, ['route' => ['admin.finances.taxes.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
    {{-- Subject --}}
    <div class="form-group col-6">
        {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Name')]) !!}
    </div>

    {{-- type --}}
    <div class="form-group col-6">
        {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}
        {!! Form::select('type', ['Percent' => 'Percent', 'Fixed' => 'Fixed'], null, ['class' => 'form-control globalOfSelect2']) !!}
    </div>

    {{-- Amount --}}
    <div class="form-group col-6">
        {{ Form::label('amount', __('Amount'), ['class' => 'col-form-label']) }}
        {!! Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __('0.0')]) !!}
    </div>

    {{-- status --}}
    <div class="form-group col-6">
        {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
        {!! Form::select('status', ['Active' => 'Active', 'Inactive' => 'Inactive'], null, ['class' => 'form-control globalOfSelect2']) !!}
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
