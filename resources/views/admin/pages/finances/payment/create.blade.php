@if ($invoicePayment->id)
    {!! Form::model($invoicePayment, ['route' => ['admin.finances.payments.update', $invoicePayment->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($invoicePayment, ['route' => ['admin.finances.payments.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
    {{-- invoice --}}
    <div class="form-group col-6">
        {{ Form::label('invoice_id', __('Invoice'), ['class' => 'col-form-label']) }}
        {!! Form::select('invoice_id', $invoice ?? [], $invoicePayment->invoice_id ?? null, [
        'data-placeholder' => 'Select Invoice',
        'class' => 'form-select globalOfSelect2Remote',
        'data-url' => route('resource-select', ['Invoice'])
        ])!!}
    </div>

    {{-- Transaction Id --}}
    <div class="form-group col-6">
        {{ Form::label('transaction_id', __('Transaction Id'), ['class' => 'col-form-label']) }}
        {!! Form::text('transaction_id', null, ['class' => 'form-control', 'placeholder' => __('Transaction Id')]) !!}
    </div>

    {{-- payment date --}}
    <div class="form-group col-6">
        {{ Form::label('payment_date', __('Payment Date'), ['class' => 'col-form-label']) }}
        {!! Form::date('payment_date', $invoicePayment->payment_date, ['class' => 'form-control flatpickr', 'placeholder' => __('Payment Date')]) !!}
    </div>

    {{-- Amount --}}
    <div class="form-group col-6">
        {{ Form::label('amount', __('Amount'), ['class' => 'col-form-label']) }}
        {!! Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __('0.0')]) !!}
    </div>

    {{-- Note --}}
    <div class="form-group col-12">
        {{ Form::label('note', __('Note'), ['class' => 'col-form-label']) }}
        {!! Form::textarea('note', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => __('Note')]) !!}
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
