@if ($invoicePayment->id)
    {!! Form::model($invoicePayment, ['route' => ['admin.finances.payments.update', $invoicePayment->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($invoicePayment, ['route' => ['admin.finances.payments.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
    {{-- Company --}}
    <div class="form-group col-6">
        {{ Form::label('company_id', __('Client'), ['class' => 'col-form-label']) }}
        {!! Form::select('company_id', $companies ?? [], $selectedCompany ?? null, [
        'data-placeholder' => __('Select Client'),
        'class' => 'form-select globalOfSelect2Remote dependent-select',
        'data-url' => route('resource-select', ['groupedCompany', 'hasinv']),
        'id' => 'payment-company-id'
        ])!!}
    </div>

      {{-- Contract --}}
    <div class="form-group col-6">
        {{ Form::label('contract_id', __('Contract'), ['class' => 'col-form-label']) }}
        {!! Form::select('contract_id', $contracts ?? [], $selectedContract ?? null, [
        'data-placeholder' => __('Select Contract'),
        'class' => 'form-select globalOfSelect2Remote dependent-select',
        'data-url' => route('resource-select', ['Contract', 'dependent' => 'company_id']),
        'data-dependent_id' => 'payment-company-id',
        'id' => 'payment-contract-id'
        ])!!}
    </div>
    {{-- invoice --}}
    <div class="form-group col-6">
        {{ Form::label('invoice_id', __('Invoice'), ['class' => 'col-form-label']) }}
        {!! Form::select('invoice_id', $invoice ?? [], $invoicePayment->invoice_id ?? null, [
        'data-placeholder' => 'Select Invoice',
        'class' => 'form-select globalOfSelect2Remote',
        'data-url' => route('resource-select', ['Invoice', 'dependent' => 'contract_id']),
        'data-dependent_id' => 'payment-contract-id',
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
