@if ($invoiceItem->id)
    {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.update', [$invoice, $invoiceItem->id, 'item' => request()->item]],
    'method' => 'PUT',
    'id' => 'item-create',
    ]) !!}
@else
    {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.store', [$invoice, 'item' => request()->item]],
    'method' => 'POST',
    'id' => 'item-create'
    ]) !!}
@endif

<div class="row">
      <span class="fw-bold">General</span>
      <hr class="hr">
      <div class="row">
        @includeWhen(request()->item != 'custom', 'admin.pages.invoices.items.edit-phases-general')
        @includeWhen(request()->item == 'custom', 'admin.pages.invoices.items.edit-custom-item-general')
          {{-- Subtotal --}}
          <div class="form-group col-6">
            {{ Form::label('subtotal', __('Subtotal'), ['class' => 'col-form-label']) }}
            {!! Form::number('subtotal', null, ['class' => 'form-control', 'disabled','placeholder' => __('Subtotal')]) !!}
          </div>
      </div>
      @if($invoice->type != 'Down Payment' && @$invoiceItem->deduction->is_before_tax && count($invoice->deductableDownpayments ?? []) > 0)
        @include('admin.pages.invoices.items.edit-deduction')
      @endif

      {{-- Include Texes Section--}}
      @include('admin.pages.invoices.items.edit-taxes')

      {{-- Deduction --}}
      @if($invoice->type != 'Down Payment' && !@$invoiceItem->deduction->is_before_tax && count($invoice->deductableDownpayments ?? []) > 0)
        @include('admin.pages.invoices.items.edit-deduction')
      @endif
      <hr class="hr mt-3" />
      {{-- Total --}}
      <div class="form-group col-6">
        <div class="d-flex justify-content-between">
          <span class="col-form-lable">Total</span>
          <label for="total" class="col-form-label pe-4">
            <label class="switch">
              <span class="switch-label fw-bold">Round Total?</span>
              {{ Form::checkbox('rounding_amount', 1, ($invoiceItem->rounding_amount ?? 0) && 1,['class' => 'switch-input'])}}
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
            </label>
          </label>
        </div>
        {!! Form::number('total', $invoiceItem->total ?? 0, ['class' => 'form-control', 'disabled','placeholder' => __('Total')]) !!}
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
  $(document).on('change', '#item-create [name="is_manual_tax"]', function(){
    if($(this).is(':checked')){
      $('#item-create [name="manual_tax_amount"]').parent().removeClass('d-none');
    }else{
      $('#item-create [name="manual_tax_amount"]').parent().addClass('d-none');
    }
  })

  // any change in the form, calculate the total
  $(document).on('keyup change paste', '#item-create input, #item-create select, #item-create checkbox', function(){
    calculateCustomItemValues();
  })
  // toggle downpayment deduction
  $(document).on('change', '#item-create [name="downpayment_id"]', function(){
    if($(this).val()){
      $('#item-create [name="dp_rate_id"]').parent().removeClass('d-none');
    }else{
      $('#item-create [name="dp_rate_id"]').parent().addClass('d-none');
    }
  })

  function calculateCustomItemValues (){
    let subtotal = getSubtotalAmount();

    if(!subtotal){
      return false;
    }

    //set subtotal
    $('#item-create [name="subtotal"]').val(subtotal.toFixed(3));

    // downpayment amount
    let totalDownpaymentAmount = calDPAmount();

    let totalTax = calItemTax();
    // total amount
    let totalAmount = subtotal + totalTax - totalDownpaymentAmount;

    // round total
    if($('#item-create [name="rounding_amount"]').is(':checked')){
      totalAmount = Math.trunc(totalAmount);
    }else{
      totalAmount = parseFloat(totalAmount).toFixed(3);
    }

    // set total amount
    $('#item-create [name="total"]').val(totalAmount);
  }

  function getSubtotalAmount(){
    const price = parseFloat($('[name="price"]').val());
    const quantity = parseFloat($('[name="quantity"]').val());
    let subtotal = price * quantity;

    // if price and quantity are not in the form, then get the subtotal from the form
    if(!subtotal){
      subtotal = parseFloat($('[name="subtotal"]').val());
    }

    return subtotal;
  }

  function calDPAmount()
  {
    let subtotal = parseFloat($('#item-create [name="subtotal"]').val());
    let totalDownpaymentAmount = 0;

    if($('#item-create [name="deduct_downpayment"]').is(':checked')){
      const downpaymentId = $('#item-create [name="downpayment_id"]').val();
      if(downpaymentId){
        var deductionRate = parseFloat($('#item-create [name="dp_rate_id"] option:selected').data('amount'));
        // is Percentage
        const isPercentageRate = $('#item-create [name="dp_rate_id"] option:selected').data('type') == 'Percent';
        if(deductionRate){
          if(!isPercentageRate){
            totalDownpaymentAmount = deductionRate;
          }else{
            // is before tax or after tax
            if($('#item-create [name="is_before_tax"]').val() == 1){
              // source
              if($('#item-create [name="calculation_source"]').val() == 'Down Payment'){
                const selectedDPTotal = parseFloat($('#item-create [name="downpayment_id"] option:selected').data('amount'));
                totalDownpaymentAmount = (selectedDPTotal * deductionRate) / 100;
              }else{
                totalDownpaymentAmount = (subtotal * deductionRate) / 100;
              }
            }else{
              // source
              if($('#item-create [name="calculation_source"]').val() == 'Down Payment'){
                const selectedDPTotal = parseFloat($('#item-create [name="downpayment_id"] option:selected').data('amount'));
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
    $('#item-create [name="downpayment_amount"]').val(totalDownpaymentAmount.toFixed(3));

    // if manual deduction is checked, use the manual deduction amount
    if($('#item-create [name="is_manual_deduction"]').is(':checked')){
      return parseFloat($('#item-create [name="manual_deduction_amount"]').val());
    }

    return totalDownpaymentAmount;
  }

  function calItemTax()
  {
    let totalTax = 0;
    if($('#item-create [name="add_tax"]').is(':checked')){
      // calculate total tax
      var taxes = $('#item-create [name="item_taxes[]"]').val();
      let subtotal = parseFloat($('#item-create [name="subtotal"]').val());
      // is deduction before tax
      if($('#item-create [name="is_before_tax"]').val() == 1){
        subtotal -= calDPAmount();
      }

      if(taxes){
        taxes.forEach(tax => {
          const taxAmount = parseFloat($('#item-create [name="item_taxes[]"] option[value="'+tax+'"]').data('amount'));
          const taxType = $('#item-create [name="item_taxes[]"] option[value="'+tax+'"]').data('type');
          if(taxType == 'Percent'){
            totalTax += (subtotal * taxAmount) / 100;
          }else{
            totalTax += taxAmount;
          }
        });
      }
    }

    // set total tax
    $('#item-create [name="total_tax_amount"]').val(totalTax.toFixed(3));

    // if manual tax is checked, use the manual tax amount
    if($('#item-create [name="is_manual_tax"]').is(':checked')){
      return parseFloat($('#item-create [name="manual_tax_amount"]').val());
    }

    return totalTax;
  }

  // toggle downpayment deduction
  $(document).on('change', '#item-create [name="deduct_downpayment"]', function(){
    if($(this).is(':checked')){
      $('#item-create [name="downpayment_id"]').parent().parent().removeClass('d-none');
    }else{
      $('#item-create [name="downpayment_id"]').parent().parent().addClass('d-none');
    }
  })

  // toggle add tax
  $(document).on('change', '#item-create [name="add_tax"]', function(){
    if($(this).is(':checked')){
      $('#item-create [name="item_taxes[]"]').parent().parent().removeClass('d-none');
    }else{
      $('#item-create [name="item_taxes[]"]').parent().parent().addClass('d-none');
    }
  })

  // on change before tax, move the deduction divs to the the bottom
  $(document).on('change', '#item-create [name="is_before_tax"]', function(){
    if($(this).val() == 0){
      // move the deduction divs to the the bottom of the taxes
      $('#item-create .deduction-section').insertAfter('#item-create .taxes-section');
    }else{
      // move the deduction divs to the the top of the taxes
      $('#item-create .deduction-section').insertBefore('#item-create .taxes-section');
    }
  })

  // toggle manual deduction
  $(document).on('change', '#item-create [name="is_manual_deduction"]', function(){
    if($(this).is(':checked')){
      $('#item-create [name="manual_deduction_amount"]').parent().removeClass('d-none');
    }else{
      $('#item-create [name="manual_deduction_amount"]').parent().addClass('d-none');
    }
  })

  // get downpayment info
  $(document).on('change', '#item-create [name="downpayment_id"]', function(){
    const downpaymentId = $(this).val();
    if(!downpaymentId){
      return false;
    }
    $.ajax({
      url: route('admin.invoices.show', { invoice: downpaymentId, downpaymentjson: '1', itemId: '{{ $invoiceItem->id }}' }),
      type: 'GET',
      success: function (response) {
        var BsAlert = `
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div>
              <strong>Total Down Payment Amount:</strong> <span class="total_amount">${response.total_amount}</span>
              <br>
              <strong>Deducted Amount:</strong> <span class="deducted_amount">${response.total_deducted_amount}</span>
              <br>
              <strong>Remaining Amount:</strong> <span class="remaining_amount">${response.total_amount - response.total_deducted_amount}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        `;
        $('#item-create .downpayment-info').html(BsAlert);
      }
    })
  })
</script>
