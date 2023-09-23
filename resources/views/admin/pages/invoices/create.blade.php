@if ($invoice->id)
    {!! Form::model($invoice, ['route' => ['admin.invoices.update', $invoice->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($invoice, ['route' => ['admin.invoices.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
    <div class="form-group col-6">
      {{ Form::label('company_id', __('Client'), ['class' => 'col-form-label']) }}
      {!! Form::select('company_id', $clients ?? [], @$invoice->bill_clientid, [
        'id' => 'client_id-select',
        'class' => 'form-select globalOfSelect2UserRemote dependent-select required',
        'data-placeholder' => 'Select Client',
        'data-url' => route('resource-select-user', ['Company'])
        ]) !!}
    </div>
    {{-- Contract --}}
    <div class="form-group col-6">
      {{ Form::label('contract_id', __('Contract'), ['class' => 'col-form-label']) }}
      {!! Form::select('contract_id', $companies ?? [], @$invoice->contract_id, [
        'class' => 'form-select globalOfSelect2Remote required',
        'data-url' => route('resource-select', ['Contract', 'dependent' => 'company_id']),
        'data-placeholder' => 'Select Contract',
        'data-dependent_id' => 'client_id-select',
      ]) !!}
    </div>
    {{-- invoice date --}}
    <div class="form-group col-6">
        {{ Form::label('invoice_date', __('Invoice Date'), ['class' => 'col-form-label']) }}
        {!! Form::text('invoice_date', null, ['class' => 'form-control flatpickr', 'placeholder' => __('Invoice Date')]) !!}
    </div>
    {{-- due date --}}
    <div class="form-group col-6">
        {{ Form::label('due_date', __('Due Date'), ['class' => 'col-form-label']) }}
        {!! Form::text('due_date', null, ['class' => 'form-control flatpickr', 'placeholder' => __('Due Date')]) !!}
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
