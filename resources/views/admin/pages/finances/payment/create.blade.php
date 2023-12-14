@if ($invoicePayment->id)
    {!! Form::model($invoicePayment, ['route' => ['admin.finances.payments.update', $invoicePayment->id, 'event' => isset($event) ? $event : '', 'table_id' => @$table_id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($invoicePayment, ['route' => ['admin.finances.payments.store', 'event' => isset($event) ? $event : '', 'table_id' => @$table_id], 'method' => 'POST']) !!}
@endif

<div class="row add-payment-form">
    {{-- Company --}}
    <div class="form-group col-6">
        {{ Form::label('company_id', __('Client'), ['class' => 'col-form-label']) }}
        {!! Form::select('company_id', $companies ?? [], $selectedCompany ?? null, [
        'data-placeholder' => __('Select Client'),
        'class' => 'form-select globalOfSelect2Remote dependent-select',
        'data-url' => route('resource-select', ['groupedCompany', 'hasinv']),
        'id' => 'payment-company-id',
        'disabled' => isset($selected_invoice)
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
        'id' => 'payment-contract-id',
        'disabled' => isset($selected_invoice)
        ])!!}
    </div>
    {{-- invoice type --}}
    <div class="form-group col-6">
        {{ Form::label('invoice_type', __('Invoice Type'), ['class' => 'col-form-label']) }}
        {!! Form::select('invoice_type', ['Invoice' => 'Customer', 'AuthorityInvoice' => 'Tax Authority'], $invoice_type ?? null, [
          'class' => 'form-select globalOfSelect2 dependent-select',
          'disabled' => isset($selected_invoice),
          'id' => 'payment-invoice-type',
        ]) !!}
    </div>
    {{-- invoice --}}
    <div class="form-group col-6">
        {{ Form::label('invoice_id', __('Invoice'), ['class' => 'col-form-label']) }}
        {!! Form::select('invoice_id', $invoiceId ?? [], $invoicePayment->payable_id ?? null, [
        'disabled' => $invoicePayment->id ? true : false,
        'data-placeholder' => 'Select Invoice',
        'data-allow-clear' => 'true',
        'class' => 'form-select globalOfSelect2Remote',
        'data-url' => route('resource-select', ['InvoiceOrAuthorityInvoice', 'dependent' => 'contract_id', 'notvoid' => '1']),
        'data-dependent_id' => 'payment-contract-id',
        'data-dependent_2' => 'payment-invoice-type',
        'disabled' => isset($selected_invoice)
        ])!!}
    </div>
    {{-- Unpaid Amount --}}
    <div class="form-group col-6">
        {{ Form::label('unpaid_amount', __('Total Balance'), ['class' => 'col-form-label']) }}
        {!! Form::number('unpaid_amount', isset($invoice) ? ($invoice->total - $invoice->paid_amount + $invoicePayment->amount) : null, ['class' => 'form-control', 'placeholder' => __('0.0'), 'disabled']) !!}
    </div>

    {{-- retentions amount --}}
    <div class="form-group col-6 retention-amount d-none">
        {{ Form::label('retention_amount', __('Retention Amount'), ['class' => 'col-form-label']) }}
        {!! Form::number('retention_amount', null, ['class' => 'form-control', 'placeholder' => __('0.0'), 'disabled']) !!}
    </div>

    {{-- payable amoun --}}
    <div class="form-group col-6 payable-amount ">
        {{ Form::label('payable_amount', __('Payable Amount'), ['class' => 'col-form-label']) }}
        {!! Form::number('payable_amount', isset($invoice) ? ($invoice->payableAmount() + $invoicePayment->amount) : null, ['class' => 'form-control', 'placeholder' => __('0.0'), 'disabled']) !!}
    </div>


    {{-- Payment Type: full, partial --}}
    <div class="form-group col-6">
        {{ Form::label('payment_type', __('Payment Type'), ['class' => 'col-form-label']) }}
        {!! Form::select('payment_type', $paymentTypes ?? ['Full' => 'Full Payment', 'Partial' => 'Partial Payment'], null, [
          'class' => 'form-select globalOfSelect2',
        ]) !!}
    </div>

    {{-- Amount --}}
    <div class="form-group col-6 d-none">
      {{ Form::label('amount', __('Amount'), ['class' => 'col-form-label']) }}
      {!! Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __('0.0')]) !!}
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

    {{-- Release Retention: none, this, all --}}
    <div class="form-group col-6 {{$invoicePayment->id ? 'd-none' : ''}}">
        {{ Form::label('release_retention', __('Release Retention'), ['class' => 'col-form-label']) }}
        {!! Form::select('release_retention', ['None' => 'None', 'This' => 'From Selected Invoice', 'All' => 'All From This Contract'], null, [
          'class' => 'form-select globalOfSelect2',
        ]) !!}
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
<script>
  // show amount when payment type is partial
  $(document).on('change', '.add-payment-form [name="payment_type"]', function() {
    if ($(this).val() == 'Partial') {
      $('.add-payment-form [name="amount"]').closest('.form-group').removeClass('d-none')
    } else {
      $('.add-payment-form [name="amount"]').closest('.form-group').addClass('d-none')
    }
  })

  // get and fill invoice data on change of invoice
  $(document).on('change', '.add-payment-form [name="invoice_id"]', function() {
    var invoiceId = $(this).val()
    var invoiceType = $('.add-payment-form [name="invoice_type"]').val()
    if (invoiceId) {
      $.ajax({
        url: invoiceType == 'Invoice' ? route('admin.invoices.show', {invoice: invoiceId, 'json': true}) : route('admin.tax-authority-invoices.show', {tax_authority_invoice: invoiceId, 'json': true}),
        type: 'GET',
        dataType: 'JSON',
        success: function(data) {
          const unpaidAmount = (data.total - data.paid_amount).toFixed(3)
          const payableAmount = (data.total - data.paid_amount - data.retention_amount - data.downpayment_amount).toFixed(3) < 0 ? 0 : (data.total - data.paid_amount - data.retention_amount - data.downpayment_amount).toFixed(3)
          $('.add-payment-form [name="unpaid_amount"]').val(unpaidAmount)
          $('.add-payment-form [name="retention_amount"]').val(data.retention_amount)
          $('.add-payment-form [name="payable_amount"]').val(payableAmount)
          $('.add-payment-form [name="amount"]').val(payableAmount)
          if (data.retention_amount > 0) {
            $('.add-payment-form .retention-amount').removeClass('d-none')
          } else {
            $('.add-payment-form .retention-amount').addClass('d-none')
          }
          if (payableAmount > 0) {
            $('.add-payment-form .payable-amount').removeClass('d-none')
          } else {
            $('.add-payment-form .payable-amount').addClass('d-none')
          }
        }
      })
    }
  })

  // amount should not be greater than payable amount
  $(document).on('change keyup', '.add-payment-form [name="amount"]', function() {
    const payableAmount = $('.add-payment-form [name="payable_amount"]').val() * 1000
    const amount = $(this).val() * 1000
    if (amount > payableAmount) {
      $(this).val(payableAmount / 1000)
    }
  })
</script>
