@if ($customInvoiceItem->id)
    {!! Form::model($customInvoiceItem, ['route' => ['admin.invoices.custom-invoice-items.update', [$invoice, $customInvoiceItem->id]],
    'method' => 'PUT',
    'id' => 'custom-item-create',
    ]) !!}
@else
    {!! Form::model($customInvoiceItem, ['route' => ['admin.invoices.custom-invoice-items.store', $invoice],
    'method' => 'POST',
    'id' => 'custom-item-create'
    ]) !!}
@endif

<div class="row">
      <span class="fw-bold">General</span>
      <hr class="hr">
      <div class="row">
        {{-- Name --}}
        <div class="form-group col-6">
          {{ Form::label('name', __('Item Name'), ['class' => 'col-form-label']) }}
          {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Item Name')]) !!}
        </div>
        {{-- price --}}
        <div class="form-group col-6">
            {{ Form::label('price', __('Price'), ['class' => 'col-form-label']) }}
            {!! Form::number('price', null, ['class' => 'form-control', 'placeholder' => __('Price')]) !!}
        </div>
        {{-- quantity --}}
        <div class="form-group col-6">
            {{ Form::label('quantity', __('Quantity'), ['class' => 'col-form-label']) }}
            {!! Form::number('quantity', null, ['class' => 'form-control', 'placeholder' => __('Quantity')]) !!}
        </div>
        {{-- Subtotal --}}
        <div class="form-group col-6">
          {{ Form::label('subtotal', __('Subtotal'), ['class' => 'col-form-label']) }}
          {!! Form::number('subtotal', null, ['class' => 'form-control', 'disabled','placeholder' => __('Subtotal')]) !!}
        </div>
      </div>
      <hr class="hr mt-3" />
      {{-- Deduct Downpayment --}}
      <div class="">
        <label class="switch">
          <span class="switch-label fw-bold">Deduct Down Payment?</span>
          {{ Form::checkbox('deduct_downpayment', 1, 0,['class' => 'switch-input'])}}
          <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
        </label>
      </div>
      <div class="row">
        {{-- Downpayment Deduction --}}
        <div class="form-group col-6 d-none">
          {{ Form::label('downpayment_id', __(' Down payment'), ['class' => 'col-form-label']) }}
          <select name="downpayment_id" id="downpayment_id" class="form-select globalOfSelect2">
            <option value="">{{__('Select Down payment')}}</option>
            @forelse ($invoice->deductableDownpayments as $dp)
              <option data-amount="{{$dp->total}}" value="{{$dp->id}}">{{runtimeInvIdFormat($dp->id)}} ( Total: @cMoney($dp->total, $invoice->contract->currency, true) )</option>
            @empty
            @endforelse
          </select>
        </div>
        {{-- Downpayment Rates --}}
        <div class="form-group col-6 d-none">
          {{ Form::label('dp_rate_id', __('Down payment Rate'), ['class' => 'col-form-label']) }}
          <select class="form-select globalOfSelect2" name="dp_rate_id" data-allow-clear='true' data-placeholder="{{__('Select Deduction Rate')}}">
            <option value="">{{__('Select Deduction Rate')}}</option>
            @forelse ($tax_rates->where('config_type', 'Down Payment') as $tax)
              <option @selected($customInvoiceItem->id && $customInvoiceItem->invoiceItem->taxes->contains($tax->id)) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
                @if($tax->type != 'Percent')
                  @cMoney($tax->amount, $invoice->contract->currency, true)
                @else
                  {{$tax->amount}}%
                @endif
              )</option>
            @empty
            @endforelse
          </select>
        </div>
        {{-- Downpayment Deduction --}}
        <div class="form-group col-6 d-none">
          {{ Form::label('downpayment_amount', __('Downpayment Amount'), ['class' => 'col-form-label']) }}
          {!! Form::number('downpayment_amount', 0, ['class' => 'form-control','placeholder' => __('Downpayment Amount')]) !!}
        </div>
      </div>
      <hr class="hr mt-3" />
      <div class="">
        <label class="switch">
          <span class="switch-label fw-bold">Add Tax?</span>
          {{ Form::checkbox('add_tax', 1, 0,['class' => 'switch-input'])}}
          <span class="switch-toggle-slider">
            <span class="switch-on"></span>
            <span class="switch-off"></span>
          </span>
        </label>
      </div>
      <div class="row d-none">
        {{-- Taxes --}}
        <div class="form-group col-6">
          {{ Form::label('item_taxes', __('Tax'), ['class' => 'col-form-label']) }}
          <select class="form-select globalOfSelect2" name="item_taxes[]" multiple data-placeholder="{{__('Select Tax')}}">
            @forelse ($tax_rates->where('config_type', 'Tax') as $tax)
              <option @selected($customInvoiceItem->id && $customInvoiceItem->invoiceItem->taxes->contains($tax->id)) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
                @if($tax->type != 'Percent')
                  @cMoney($tax->amount, $invoice->contract->currency, true)
                @else
                  {{$tax->amount}}%
                @endif
              )</option>
            @empty
            @endforelse
          </select>
        </div>
        {{-- Tax Value --}}
        <div class="form-group col-6">
          {{ Form::label('total_tax_amount', __('Tax Value'), ['class' => 'col-form-label']) }}
          {!! Form::number('total_tax_amount', $customInvoiceItem->invoiceItem->total_tax_amount ?? 0, ['class' => 'form-control', 'disabled','placeholder' => __('Tax Value')]) !!}
        </div>
        {{-- Adjust Tax --}}
        <div class="col-6 mt-1">
          <label class="switch mt-4">
            {{ Form::checkbox('is_manual_tax', 1, $customInvoiceItem->id && $customInvoiceItem->invoiceItem->manual_tax_amount > 0 ? 1 : 0,['class' => 'switch-input'])}}
            <span class="switch-toggle-slider">
              <span class="switch-on"></span>
              <span class="switch-off"></span>
            </span>
            <span class="switch-label">Adjust tax</span>
          </label>
        </div>
        {{-- Adjusted Tax --}}
        <div class="form-group col-6 {{$customInvoiceItem->id && $customInvoiceItem->invoiceItem->manual_tax_amount > 0 ? '' : 'd-none'}}">
          {{ Form::label('manual_tax_amount', __('Adjusted Tax'), ['class' => 'col-form-label']) }}
          {!! Form::number('manual_tax_amount', $customInvoiceItem->invoiceItem->manual_tax_amount ?? 0, ['class' => 'form-control', 'placeholder' => __('Adjusted Tax')]) !!}
        </div>
        {{-- Total --}}
        <div class="form-group col-6">
          {{ Form::label('total', __('Total'), ['class' => 'col-form-label']) }}
          {!! Form::number('total', $customInvoiceItem->invoiceItem->total ?? 0, ['class' => 'form-control', 'disabled','placeholder' => __('Total')]) !!}
        </div>
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
  // calcualte total tax
  $(document).on('change', '#custom-item-create [name="item_taxes[]"]', function(){
    calculateCustomItemValues();
  })
  // calcualte total, quantity * price
  $(document).on('change keyup', '[name="quantity"], [name="price"]', function(){
    calculateCustomItemValues();
  })

  // toggle manual tax
  $(document).on('change', '#custom-item-create [name="is_manual_tax"]', function(){
    if($(this).is(':checked')){
      $('#custom-item-create [name="manual_tax_amount"]').parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="manual_tax_amount"]').parent().addClass('d-none');
    }
    calculateCustomItemValues();
  })

  // onchange manual tax amount recalculate total
  $(document).on('change keyup', '#custom-item-create [name="manual_tax_amount"]', function(){
    calculateCustomItemValues();
  })

  // toggle downpayment deduction
  $(document).on('change', '#custom-item-create [name="downpayment_id"]', function(){
    if($(this).val()){
      $('#custom-item-create [name="dp_rate_id"]').parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="dp_rate_id"]').parent().addClass('d-none');
    }
    calculateCustomItemValues();
  })

  function calculateCustomItemValues (){
    const price = parseFloat($('[name="price"]').val());
    const quantity = parseFloat($('[name="quantity"]').val());
    let subtotal = price * quantity;

    //set subtotal
    $('#custom-item-create [name="subtotal"]').val(subtotal.toFixed(3));

    // downpayment amount
    if($('#custom-item-create [name="downpayment_id"]').val()){
      subtotal -= parseFloat($('#custom-item-create [name="downpayment_amount"]').val());
    }


    let totalTax = 0;
    // calculate total tax
    var taxes = $('#custom-item-create [name="item_taxes[]"]').val();
    if(taxes){
      taxes.forEach(tax => {
        const taxAmount = parseFloat($('#custom-item-create [name="item_taxes[]"] option[value="'+tax+'"]').data('amount'));
        const taxType = $('#custom-item-create [name="item_taxes[]"] option[value="'+tax+'"]').data('type');
        if(taxType == 'Percent'){
          totalTax += (subtotal * taxAmount) / 100;
        }else{
          totalTax += taxAmount;
        }
      });
    }

    // total amount
    let totalAmount = subtotal + totalTax
    // if manual tax is checked, use the manual tax amount
    if($('#custom-item-create [name="is_manual_tax"]').is(':checked')){
      const manualTax = parseFloat($('#custom-item-create [name="manual_tax_amount"]').val());
      totalAmount = subtotal + manualTax;
    }

    // set total tax and total amount
    $('#custom-item-create [name="total_tax_amount"]').val(totalTax.toFixed(3));
    $('#custom-item-create [name="total"]').val(totalAmount.toFixed(3));
  }

  // toggle downpayment deduction
  $(document).on('change', '#custom-item-create [name="deduct_downpayment"]', function(){
    if($(this).is(':checked')){
      $('#custom-item-create [name="downpayment_id"]').parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="downpayment_id"]').parent().addClass('d-none');
    }
    calculateCustomItemValues();
  })

  // toggle add tax
  $(document).on('change', '#custom-item-create [name="add_tax"]', function(){
    if($(this).is(':checked')){
      $('#custom-item-create [name="item_taxes[]"]').parent().parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="item_taxes[]"]').parent().parent().addClass('d-none');
    }
    calculateCustomItemValues();
  })
</script>
