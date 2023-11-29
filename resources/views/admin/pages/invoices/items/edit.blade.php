@if ($invoiceItem->id)
  @if(!isset($tax_rates) && !isset($deduction_rates))
    {{-- item update --}}
    {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.update', [$invoice, $invoiceItem->id, 'item' => request()->item]],
    'method' => 'PUT',
    'id' => 'item-create',
    ]) !!}
  @elseif(isset($tax_rates) && isset($pivot_tax))
    {{-- Item Tax Update --}}
    {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.taxes.update', [$invoice, $invoiceItem->id, $pivot_tax, 'item' => request()->item, 'tab' => request()->tab]],
    'method' => 'PUT',
    'id' => 'item-create',
    ]) !!}
  @elseif(isset($deduction_rates) && isset($added_deduction))
    {{-- Item Deduction Update --}}
    {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.deductions.update', [$invoice, $invoiceItem->id, $added_deduction, 'item' => request()->item, 'tab' => request()->tab]],
    'method' => 'PUT',
    'id' => 'item-create',
    ]) !!}
  @elseif(isset($tax_rates))
    {{-- Item Tax Store --}}
    {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.taxes.store', [$invoice, $invoiceItem->id, 'item' => request()->item, 'tab' => request()->tab]],
    'method' => 'POST',
    'id' => 'item-create',
    ]) !!}
  @elseif(isset($deduction_rates))
    {{-- Item Deduction Store --}}
    {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.deductions.store', [$invoice, $invoiceItem->id, 'item' => request()->item, 'tab' => request()->tab]],
    'method' => 'POST',
    'id' => 'item-create',
    ]) !!}
  @endif
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
        @php
          $disabled = isset($tax_rates) || isset($deduction_rates) ? 'disabled' : '';
        @endphp
        @includeWhen((request()->item != 'custom'), 'admin.pages.invoices.items.edit-phases-general')
        @includeWhen((request()->item == 'custom'), 'admin.pages.invoices.items.edit-custom-item-general')
          {{-- Subtotal --}}
          <div class="form-group col-6">
            {{ Form::label('subtotal', __('Subtotal'), ['class' => 'col-form-label']) }}
            {!! Form::number('subtotal', null, ['class' => 'form-control', 'disabled','placeholder' => __('Subtotal')]) !!}
          </div>
      </div>
      <div class="row">
        @if(isset($deduction_rates))
          {{-- total_tax_amount --}}
          <div class="form-group col-6">
            {{ Form::label('t_total_tax_amount', __('Total Tax'), ['class' => 'col-form-label']) }}
            {!! Form::number('t_total_tax_amount', $invoiceItem->total_tax_amount, ['class' => 'form-control', 'disabled','placeholder' => __('Total Tax')]) !!}
          </div>
          {{-- total --}}
          <div class="form-group col-6">
            {{ Form::label('t_total', __('Total'), ['class' => 'col-form-label']) }}
            {!! Form::number('t_total', $invoiceItem->total, ['class' => 'form-control', 'disabled','placeholder' => __('Total')]) !!}
          </div>
        @endif
        {{-- decription --}}
        <div class="form-group col-12">
          {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
          {!! Form::text('description', null, ['class' => 'form-control', $disabled, 'placeholder' => __('Description')]) !!}
        </div>
      </div>
      {{-- Include Texes Section--}}
      @includeWhen(isset($tax_rates), 'admin.pages.invoices.items.edit-taxes')
      {{-- @include('admin.pages.invoices.items.edit-taxes') --}}

      {{-- Deduction --}}
      @if(isset($deduction_rates) && $invoice->type != 'Down Payment' && count($invoice->deductableDownpayments ?? []) > 0)
        @include('admin.pages.invoices.items.edit-deduction')
      @endif
      @if(isset($tax_rates) || isset($deduction_rates))
        <hr class="hr mt-3" />
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
          {!! Form::number('total', $invoiceItem->subtotal ?? 0, ['class' => 'form-control', 'disabled','placeholder' => __('Total')]) !!}
        </div>
      @endif
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
    $('#item-create [name="total_tax_amount"]').prop('disabled', !$(this).is(':checked'));
  })

  // any change in the form, calculate the total
  $(document).on('keyup change paste', '#item-create input, #item-create select, #item-create checkbox', function(){
    calculateCustomItemValues();
  })
  // toggle downpayment deduction
  $(document).on('change', '#item-create [name="downpayment_id"]', function(){
    if($(this).val() && !$('#item-create [name="is_fixed_amount"]').is(':checked')){
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

    // if tax category is 2, then make total tax negative
    const taxCategory = $('#item-create [name="item_tax"]').find('option:selected').data('category');
    if(taxCategory == 2){
      totalTax = -totalTax;
    }else if(taxCategory == 3){
      totalTax = 0;
    }
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
    // if s_total is present in form
    if($('#item-create [name="t_total"]').length > 0 && $('#item-create [name="is_before_tax"]').val() == 0){
      subtotal = parseFloat($('#item-create [name="t_total"]').val());
    }
    let totalDownpaymentAmount = 0;

    const downpaymentId = $('#item-create [name="downpayment_id"]').val();
    if(downpaymentId && !$('#item-create [name="is_manual_deduction"]').is(':checked') && !$('#item-create [name="is_fixed_amount"]').is(':checked')){
      var deductionRate = parseFloat($('#item-create [name="dp_rate_id"] option:selected').data('amount'));
      // is Percentage
      const isPercentageRate = $('#item-create [name="dp_rate_id"] option:selected').data('type') == 'Percent';
      if(deductionRate){
        if(!isPercentageRate){
          totalDownpaymentAmount = deductionRate;
        }else{
          // is before tax or after tax
          if($('#item-create [name="is_before_tax"]').val() == 0){
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

    if($('#item-create [name="is_manual_deduction"]').is(':checked') || $('#item-create [name="is_fixed_amount"]').is(':checked')){
      totalDownpaymentAmount = parseFloat($('#item-create [name="downpayment_amount"]').val());
    }else{
      // set downpayment amount
      $('#item-create [name="downpayment_amount"]').val(totalDownpaymentAmount.toFixed(3));
    }

    return totalDownpaymentAmount;
  }

  function calItemTax()
  {
    let totalTax = 0;
    // calculate total tax
    var tax= $('#item-create [name="item_tax"]').val();
    if(tax && !$('#item-create [name="is_manual_tax"]').is(':checked')){
      let subtotal = parseFloat($('#item-create [name="subtotal"]').val());
      // is deduction before tax
      if($('#item-create [name="is_before_tax"]').val() == 1){
        subtotal -= calDPAmount();
      }

      if(tax){
        const taxAmount = parseFloat($('#item-create [name="item_tax"] option[value="'+tax+'"]').data('amount'));
        const taxType = $('#item-create [name="item_tax"] option[value="'+tax+'"]').data('type');
        if(taxType == 'Percent'){
          totalTax += (subtotal * taxAmount) / 100;
        }else{
          totalTax += taxAmount;
        }
      }
    }

    if($('#item-create [name="is_manual_tax"]').is(':checked')){
      totalTax = parseFloat($('#item-create [name="total_tax_amount"]').val());
    }else{
      // set total tax
      $('#item-create [name="total_tax_amount"]').val(totalTax.toFixed(3));
    }

    return totalTax;
  }

  // toggle manual deduction
  $(document).on('change', '#item-create [name="is_manual_deduction"]', function(){
    $('#item-create [name="downpayment_amount"]').prop('disabled', !$(this).is(':checked'));
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

  // on change is_fixed_amount, show/hide and cal-deduction-section
  $(document).on('change', '#item-create [name="is_fixed_amount"]', function(){
    if($(this).is(':checked')){
      $('#item-create .cal-deduction-section').addClass('d-none');
      // enable downpayment_amount
      $('#item-create [name="downpayment_amount"]').prop('disabled', false);
    }else{
      $('#item-create .cal-deduction-section').removeClass('d-none');
      // disable downpayment_amount
      $('#item-create [name="downpayment_amount"]').prop('disabled', true);
    }
  })
</script>
