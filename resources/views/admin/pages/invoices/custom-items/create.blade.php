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
      @if(@$customInvoiceItem->invoiceItem->deduction->is_before_tax)
        @include('admin.pages.invoices.custom-items.edit-deduction')
      @endif
      <div class="taxes-section">
        <hr class="hr mt-3" />
        <div class="">
          <label class="switch">
            <span class="switch-label fw-bold">Add Tax?</span>
            {{ Form::checkbox('add_tax', 1, count($customInvoiceItem->invoiceItem->taxes ?? []) && 1,['class' => 'switch-input'])}}
            <span class="switch-toggle-slider">
              <span class="switch-on"></span>
              <span class="switch-off"></span>
            </span>
          </label>
        </div>
        <div class="row {{count($customInvoiceItem->invoiceItem->taxes ?? []) ? '' : 'd-none'}}">
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
        </div>
      </div>
      @if(!@$customInvoiceItem->invoiceItem->deduction->is_before_tax)
        @include('admin.pages.invoices.custom-items.edit-deduction')
      @endif
      <hr class="hr mt-3" />
      {{-- Total --}}
      <div class="form-group col-6">
        <div class="d-flex justify-content-between">
          <span class="col-form-lable">Total</span>
          <label for="total" class="col-form-label pe-4">
            <label class="switch">
              <span class="switch-label fw-bold">Round Total?</span>
              {{ Form::checkbox('rounding_amount', 1, ($customInvoiceItem->invoiceItem->rounding_amount ?? 0) && 1,['class' => 'switch-input'])}}
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
            </label>
          </label>
        </div>
        {!! Form::number('total', $customInvoiceItem->invoiceItem->total ?? 0, ['class' => 'form-control', 'disabled','placeholder' => __('Total')]) !!}
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
  // toggle manual tax
  $(document).on('change', '#custom-item-create [name="is_manual_tax"]', function(){
    if($(this).is(':checked')){
      $('#custom-item-create [name="manual_tax_amount"]').parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="manual_tax_amount"]').parent().addClass('d-none');
    }
  })

  // any change in the form, calculate the total
  $(document).on('keyup change paste', '#custom-item-create input, #custom-item-create select, #custom-item-create checkbox', function(){
    calculateCustomItemValues();
  })
  // toggle downpayment deduction
  $(document).on('change', '#custom-item-create [name="downpayment_id"]', function(){
    if($(this).val()){
      $('#custom-item-create [name="dp_rate_id"]').parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="dp_rate_id"]').parent().addClass('d-none');
    }
  })

  function calculateCustomItemValues (){
    const price = parseFloat($('[name="price"]').val());
    const quantity = parseFloat($('[name="quantity"]').val());
    let subtotal = price * quantity;

    if(!subtotal){
      return false;
    }

    //set subtotal
    $('#custom-item-create [name="subtotal"]').val(subtotal.toFixed(3));

    // downpayment amount
    let totalDownpaymentAmount = calDPAmount();

    let totalTax = calItemTax();
    // total amount
    let totalAmount = subtotal + totalTax - totalDownpaymentAmount;

    // round total
    if($('#custom-item-create [name="rounding_amount"]').is(':checked')){
      totalAmount = Math.trunc(totalAmount);
    }else{
      totalAmount = parseFloat(totalAmount).toFixed(3);
    }

    // set total amount
    $('#custom-item-create [name="total"]').val(totalAmount);
  }
  function calDPAmount()
  {
    let subtotal = parseFloat($('#custom-item-create [name="subtotal"]').val());
    let totalDownpaymentAmount = 0;

    if($('#custom-item-create [name="deduct_downpayment"]').is(':checked')){
      const downpaymentId = $('#custom-item-create [name="downpayment_id"]').val();
      if(downpaymentId){
        var deductionRate = parseFloat($('#custom-item-create [name="dp_rate_id"] option:selected').data('amount'));
        // is Percentage
        const isPercentageRate = $('#custom-item-create [name="dp_rate_id"] option:selected').data('type') == 'Percent';
        if(deductionRate){
          if(!isPercentageRate){
            totalDownpaymentAmount = deductionRate;
          }else{
            // is before tax or after tax
            if($('#custom-item-create [name="is_before_tax"]').val() == 1){
              // source
              if($('#custom-item-create [name="calculation_source"]').val() == 'Down Payment'){
                const selectedDPTotal = parseFloat($('#custom-item-create [name="downpayment_id"] option:selected').data('amount'));
                totalDownpaymentAmount = (selectedDPTotal * deductionRate) / 100;
              }else{
                totalDownpaymentAmount = (subtotal * deductionRate) / 100;
              }
            }else{
              // source
              if($('#custom-item-create [name="calculation_source"]').val() == 'Down Payment'){
                const selectedDPTotal = parseFloat($('#custom-item-create [name="downpayment_id"] option:selected').data('amount'));
                totalDownpaymentAmount = (selectedDPTotal * deductionRate) / 100;
              }else{
                totalDownpaymentAmount = ((subtotal + calItemTax()) * deductionRate) / 100;
              }
            }
          }
        }
      }
    }

    // set downpayment amount
    $('#custom-item-create [name="downpayment_amount"]').val(totalDownpaymentAmount.toFixed(3));

    // if manual deduction is checked, use the manual deduction amount
    if($('#custom-item-create [name="is_manual_deduction"]').is(':checked')){
      return parseFloat($('#custom-item-create [name="manual_deduction_amount"]').val());
    }

    return totalDownpaymentAmount;
  }

  function calItemTax()
  {
    let totalTax = 0;
    // calculate total tax
    if($('#custom-item-create [name="add_tax"]').is(':checked')){
      var taxes = $('#custom-item-create [name="item_taxes[]"]').val();
      let subtotal = parseFloat($('#custom-item-create [name="subtotal"]').val());
      // is deduction before tax
      if($('#custom-item-create [name="is_before_tax"]').val() == 1){
        subtotal -= calDPAmount();
      }

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
    }
    // set total tax
    $('#custom-item-create [name="total_tax_amount"]').val(totalTax.toFixed(3));

    // if manual tax is checked, use the manual tax amount
    if($('#custom-item-create [name="is_manual_tax"]').is(':checked')){
      return parseFloat($('#custom-item-create [name="manual_tax_amount"]').val());
    }

    return totalTax;
  }

  // toggle downpayment deduction
  $(document).on('change', '#custom-item-create [name="deduct_downpayment"]', function(){
    if($(this).is(':checked')){
      $('#custom-item-create [name="downpayment_id"]').parent().parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="downpayment_id"]').parent().parent().addClass('d-none');
    }
  })

  // toggle add tax
  $(document).on('change', '#custom-item-create [name="add_tax"]', function(){
    if($(this).is(':checked')){
      $('#custom-item-create [name="item_taxes[]"]').parent().parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="item_taxes[]"]').parent().parent().addClass('d-none');
    }
  })

  // on change before tax, move the deduction divs to the the bottom
  $(document).on('change', '#custom-item-create [name="is_before_tax"]', function(){
    if($(this).val() == 0){
      // move the deduction divs to the the bottom of the taxes
      $('#custom-item-create .deduction-section').insertAfter('#custom-item-create .taxes-section');
    }else{
      // move the deduction divs to the the top of the taxes
      $('#custom-item-create .deduction-section').insertBefore('#custom-item-create .taxes-section');
    }
  })

  // toggle manual deduction
  $(document).on('change', '#custom-item-create [name="is_manual_deduction"]', function(){
    if($(this).is(':checked')){
      $('#custom-item-create [name="manual_deduction_amount"]').parent().removeClass('d-none');
    }else{
      $('#custom-item-create [name="manual_deduction_amount"]').parent().addClass('d-none');
    }
  })
</script>
