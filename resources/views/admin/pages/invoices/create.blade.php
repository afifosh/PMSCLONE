@if ($invoice->id)
    {!! Form::model($invoice, ['route' => ['admin.invoices.update', $invoice->id], 'method' => 'PUT']) !!}
@else
    {!! Form::model($invoice, ['route' => ['admin.invoices.store'], 'method' => 'POST']) !!}
@endif

<div class="row">
    <div class="form-group  col-12">
      {{-- {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }} --}}
      <div class="row">
        <div class="col-md mb-md-0 mb-3">
          <div class="form-check custom-option custom-option-icon {{!$invoice->type || $invoice->type == 'Reqular' ? 'checked': ''}}">
            <label class="form-check-label custom-option-content" for="customRadioRegular">
              <span class="custom-option-body">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-skyscraper" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                      <path d="M3 21l18 0"></path>
                      <path d="M5 21v-14l8 -4v18"></path>
                      <path d="M19 21v-10l-6 -4"></path>
                      <path d="M9 9l0 .01"></path>
                      <path d="M9 12l0 .01"></path>
                      <path d="M9 15l0 .01"></path>
                      <path d="M9 18l0 .01"></path>
                  </svg>
                <span class="custom-option-title">Regular</span>
              </span>
              {!! Form::radio('type', 'Regular', false, ['class' => 'form-check-input', 'id' => 'customRadioRegular', 'checked']) !!}
            </label>
          </div>
        </div>
        <div class="col-md mb-md-0 mb-3">
          <div class="form-check custom-option custom-option-icon {{$invoice->type == 'Down Payment' ? 'checked': ''}}">
            <label class="form-check-label custom-option-content" for="customRadioDown Payment">
              <span class="custom-option-body">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                      <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                      <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                  </svg>
                <span class="custom-option-title"> Down Payment </span>
              </span>
              {!! Form::radio('type', 'Down Payment', false, ['class' => 'form-check-input', 'id' => 'customRadioDown Payment']) !!}
            </label>
          </div>
        </div>
      </div>
      <small class="form-text text-muted">{{ __('Select invoice type: Regular or Down Payment') }}</small>
    </div>
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
      {{-- amount --}}
      <div class="form-group col-6 downpayment-amount d-none">
          {{ Form::label('subtotal', __('Amount'), ['class' => 'col-form-label']) }}
          <div class="dropdown open d-inline">
            <span data-bs-toggle="dropdown" aria-haspopup="true">
                <i class="fas fa-calculator"></i>
            </span>
            <div class="dropdown-menu p-3">
              <div class="mb-3" data-content="percent-cal">
                <label for="percent-value" class="form-label">Percentage (Balance: <span id="contract-remaining-balance">0</span>)</label>
                <input type="number" name="percent-value" id="percent-value" class="form-control" placeholder="10%">
              </div>
            </div>
          </div>
          {!! Form::number('subtotal', null, ['class' => 'form-control', 'placeholder' => __('Amount')]) !!}
      </div>
      {{-- description --}}
      <div class="form-group col-6 downpayment-amount d-none">
          {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
          {!! Form::text('description', null, ['class' => 'form-control', 'placeholder' => __('Description')]) !!}
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
  $(document).on('change', '[name="type"]', function(){
    $(this).parents('.form-check').addClass('checked');
    $(this).parents('.form-check').parent().siblings().find('.form-check').removeClass('checked');
    if($(this).val() == 'Down Payment'){
      $('.downpayment-amount').removeClass('d-none');
    }else{
      $('.downpayment-amount').addClass('d-none');
    }
  })
  $(document).on('change', '[name="contract_id"]', function(){
    var contract_id = $(this).val();
    if(contract_id)
    $.ajax({
      url: route('admin.contracts.show', {contract: contract_id, getjson: true}),
      type: 'GET',
      success: function(data){
        $('#contract-remaining-balance').text(data.remaining_amount);
      }
    })
  });
  $(document).on('change keyup', '#percent-value', function(){
    var percent = $(this).val();
    var balance = $('#contract-remaining-balance').text();
    var amount = (balance * percent) / 100;
    $('[name="subtotal"]').val(amount);
  })
</script>
