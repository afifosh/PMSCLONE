{!! Form::open(['route' => ['admin.invoices.release-retention', [$invoice]], 'method' => 'POST']) !!}
<div class="row add-payment-form">
    {{-- Company --}}
    <div class="form-group col-6">
        {{ Form::label('company_id', __('Client'), ['class' => 'col-form-label']) }}
        {!! Form::select('company_id', [$invoice->contract->assignable_id => $invoice->contract->assignable->name], $invoice->contract->assignable_id, [
        'data-placeholder' => __('Select Client'),
        'class' => 'form-select globalOfSelect2Remote dependent-select',
        'data-url' => route('resource-select', ['groupedCompany', 'hasinv']),
        'id' => 'payment-company-id',
        'disabled' => 'true',
        ])!!}
    </div>

      {{-- Contract --}}
    <div class="form-group col-6">
        {{ Form::label('contract_id', __('Contract'), ['class' => 'col-form-label']) }}
        {!! Form::select('contract_id', [$invoice->contract_id => $invoice->contract->subject], $invoice->contract_id, [
        'data-placeholder' => __('Select Contract'),
        'class' => 'form-select globalOfSelect2Remote dependent-select',
        'data-url' => route('resource-select', ['Contract', 'dependent' => 'company_id']),
        'data-dependent_id' => 'payment-company-id',
        'id' => 'payment-contract-id',
        'disabled' => 'true'
        ])!!}
    </div>
    {{-- invoice type --}}
    <div class="form-group col-6">
        {{ Form::label('invoice_type', __('Invoice Type'), ['class' => 'col-form-label']) }}
        {!! Form::select('invoice_type', ['Invoice' => 'Customer'], 'Invoice', [
          'class' => 'form-select globalOfSelect2 dependent-select',
          'disabled' => 'true',
          'id' => 'payment-invoice-type'
        ]) !!}
    </div>
    {{-- invoice --}}
    <div class="form-group col-6">
        {{ Form::label('invoice_id', __('Invoice'), ['class' => 'col-form-label']) }}
        {!! Form::select('invoice_id', [$invoice->id => $invoice->getFormatedId()], $invoice->id, [
        'disabled' => true,
        'class' => 'form-select',
        'id' => 'payment-invoice-id',
        ])!!}
    </div>

    {{-- retentions amount --}}
    <div class="form-group col-6 retention-amount d-none">
        {{ Form::label('retention_amount', __('Retention Amount'), ['class' => 'col-form-label']) }}
        {!! Form::number('retention_amount', $invoice->retention_amount, ['class' => 'form-control', 'placeholder' => __('0.0'), 'disabled']) !!}
    </div>

    <div class="form-group col-6">
        {{ Form::label('payment_type', __('Payment Type'), ['class' => 'col-form-label']) }}
        {!! Form::select('payment_type', ['Full' => 'Retention Release'], 'Full', [
          'class' => 'form-select globalOfSelect2',
          'id' => 'invoice-payment-type',
          'disabled' => 'true'
          ]) !!}
    </div>

    {{-- Account --}}
  <div class="form-group col-6">
    {{ Form::label('account_balance_id', __('Account'), ['class' => 'col-form-label']) }}
    {!! Form::select('account_balance_id', [], null, [
    'class' => 'form-select globalOfSelect2Remote',
    'data-url' => route('resource-select', ['AccountBalance', 'dependent' => 'invoice_id', 'dependent_2_col' => 'inv-type', 'dependent_3_col' => 'pay-type']),
    'data-dependent_id' => 'payment-invoice-id',
    'data-dependent_2' => 'payment-invoice-type',
    'data-dependent_3' => 'invoice-payment-type',
    'data-placeholder' => __('Select Account'),
    'data-allow-clear' => 'true'
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
