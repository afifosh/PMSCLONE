@if ($invoiceItem->id)
    {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.update', [$invoice, $invoiceItem->id]],
    'method' => 'PUT',
    'id' => 'item-create',
    ]) !!}
@else
    {{-- {!! Form::model($invoiceItem, ['route' => ['admin.invoices.invoice-items.store', $invoice],
    'method' => 'POST',
    'id' => 'item-create'
    ]) !!} --}}
@endif

<div class="row">
      <span class="fw-bold">General</span>
      <hr class="hr">
      <div class="row">
        {{-- Stage --}}
      <div class="form-group col-6">
        {{ Form::label('stage_id', __('Stage'), ['class' => 'col-form-label']) }}
        {!! Form::select('stage_id', $stages ?? [], $invoiceItem->invoiceable->stage_id ?? null, ['class' => 'form-control globalOfSelect2', 'disabled', 'placeholder' => __('Select Stage')]) !!}
      </div>
      {{-- Phase --}}
      <div class="form-group col-6">
        {{ Form::label('phase_id', __('Phase'), ['class' => 'col-form-label']) }}
        {!! Form::select('phase_id', $phases ?? [], $invoiceItem->invoiceable_id ?? null, ['class' => 'form-control globalOfSelect2', 'disabled', 'placeholder' => __('Select Phase')]) !!}
      </div>
      {{-- Subtotal --}}
      <div class="form-group col-6">
        {{ Form::label('subtotal', __('Subtotal'), ['class' => 'col-form-label']) }}
        {!! Form::number('subtotal', null, ['class' => 'form-control', 'disabled','placeholder' => __('Subtotal')]) !!}
      </div>
        {{-- Subtotal --}}
        <div class="form-group col-6">
          {{ Form::label('subtotal', __('Subtotal'), ['class' => 'col-form-label']) }}
          {!! Form::number('subtotal', null, ['class' => 'form-control', 'disabled','placeholder' => __('Subtotal')]) !!}
        </div>
      </div>
      <div class="deduction-section">
        <hr class="hr mt-3" />
        {{-- Deduct Downpayment --}}
        <div class="">
          <label class="switch">
            <span class="switch-label fw-bold">Deduct Down Payment?</span>
            {{ Form::checkbox('deduct_downpayment', 1, @$invoiceItem->deduction->id && 1,['class' => 'switch-input'])}}
            <span class="switch-toggle-slider">
              <span class="switch-on"></span>
              <span class="switch-off"></span>
            </span>
          </label>
        </div>
        <div class="row {{($invoiceItem->deduction->id ?? 0) ? '' : 'd-none'}}">
          {{-- is_before_tax --}}
          <div class="form-group col-6">
            {{ Form::label('is_before_tax', __('Before Tax'), ['class' => 'col-form-label']) }}
            {!! Form::select('is_before_tax', ['1' => 'Yes', '0' => 'No'], $invoiceItem->is_before_tax ?? 1, ['class' => 'form-select globalOfSelect2', 'id' => 'is_before_tax']) !!}
          </div>
          {{-- Downpayment Deduction --}}
          <div class="form-group col-6">
            {{ Form::label('downpayment_id', __(' Down payment'), ['class' => 'col-form-label']) }}
            <select name="downpayment_id" id="downpayment_id" class="form-select globalOfSelect2">
              <option value="">{{__('Select Down payment')}}</option>
              @forelse ($invoice->deductableDownpayments as $dp)
                <option data-amount="{{$dp->total}}" value="{{$dp->id}}" @selected($invoiceItem->deduction->downpayment_id ?? 0 == $dp->id)>{{runtimeInvIdFormat($dp->id)}} ( Total: @cMoney($dp->total, $invoice->contract->currency, true) )</option>
              @empty
              @endforelse
            </select>
          </div>
          {{-- Downpayment Rates --}}
          <div class="form-group col-6">
            {{ Form::label('dp_rate_id', __('Down payment Rate'), ['class' => 'col-form-label']) }}
            <select class="form-select globalOfSelect2" name="dp_rate_id" data-allow-clear='true' data-placeholder="{{__('Select Deduction Rate')}}">
              <option value="">{{__('Select Deduction Rate')}}</option>
              @forelse ($tax_rates->where('config_type', 'Down Payment') as $tax)
                <option @selected($invoiceItem->deduction->dp_rate_id ?? 0 == $tax->id) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
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
          {{-- calculation_source --}}
          <div class="form-group col-6">
            {{ Form::label('calculation_source', __('Percentage Calculation Source'), ['class' => 'col-form-label']) }}
            {!! Form::select('calculation_source', ['Down Payment' => 'Down Payment Total', 'Deductible' => 'Item Total'], $invoiceItem->deduction->calculation_source ??null, [
              'class' => 'form-control globalOfSelect2',
              'data-placeholder' => __('Calculation Source'),
              'data-allow-clear' => 'true',
            ])!!}
          </div>
          {{-- Downpayment Deduction --}}
          <div class="form-group col-6">
            {{ Form::label('downpayment_amount', __('Downpayment Amount'), ['class' => 'col-form-label']) }}
            {!! Form::number('downpayment_amount', $invoiceItem->deduction->amount ?? 1, ['class' => 'form-control', 'disabled', 'placeholder' => __('Downpayment Amount')]) !!}
          </div>
          {{-- Adjust Deduction --}}
          <div class="col-6 mt-3">
            <label class="switch mt-4">
              {{ Form::checkbox('is_manual_deduction', 1, ($invoiceItem->deduction->manual_amount ?? 0) && 1,['class' => 'switch-input'])}}
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
              <span class="switch-label">Adjust Deduction Amount</span>
            </label>
          </div>
          {{-- Adjusted Deduction Amount --}}
          <div class="form-group col-6 {{($invoiceItem->deduction->manual_amount ?? 0) ? '' : 'd-none'}}">
            {{ Form::label('manual_deduction_amount', __('Adjusted Deduction Amount'), ['class' => 'col-form-label']) }}
            {!! Form::number('manual_deduction_amount', $invoiceItem->deduction->manual_amount ?? 0, ['class' => 'form-control', 'placeholder' => __('Adjusted Deduction Amount')]) !!}
          </div>
        </div>
      </div>
      <div class="taxes-section">
        <hr class="hr mt-3" />
        <div class="">
          <label class="switch">
            <span class="switch-label fw-bold">Add Tax?</span>
            {{ Form::checkbox('add_tax', 1, count($invoiceItem->taxes ?? []) && 1,['class' => 'switch-input'])}}
            <span class="switch-toggle-slider">
              <span class="switch-on"></span>
              <span class="switch-off"></span>
            </span>
          </label>
        </div>
        <div class="row {{count($invoiceItem->taxes ?? []) ? '' : 'd-none'}}">
          {{-- Taxes --}}
          <div class="form-group col-6">
            {{ Form::label('item_taxes', __('Tax'), ['class' => 'col-form-label']) }}
            <select class="form-select globalOfSelect2" name="item_taxes[]" multiple data-placeholder="{{__('Select Tax')}}">
              @forelse ($tax_rates->where('config_type', 'Tax') as $tax)
                <option @selected($invoiceItem->id && $invoiceItem->taxes->contains($tax->id)) data-amount="{{$tax->amount}}" data-type="{{$tax->type}}" value="{{$tax->id}}">{{$tax->name}} (
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
            {!! Form::number('total_tax_amount', $invoiceItem->total_tax_amount ?? 0, ['class' => 'form-control', 'disabled','placeholder' => __('Tax Value')]) !!}
          </div>
          {{-- Adjust Tax --}}
          <div class="col-6 mt-1">
            <label class="switch mt-4">
              {{ Form::checkbox('is_manual_tax', 1, $invoiceItem->id && $invoiceItem->manual_tax_amount > 0 ? 1 : 0,['class' => 'switch-input'])}}
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
              <span class="switch-label">Adjust tax</span>
            </label>
          </div>
          {{-- Adjusted Tax --}}
          <div class="form-group col-6 {{$invoiceItem->id && $invoiceItem->manual_tax_amount > 0 ? '' : 'd-none'}}">
            {{ Form::label('manual_tax_amount', __('Adjusted Tax'), ['class' => 'col-form-label']) }}
            {!! Form::number('manual_tax_amount', $invoiceItem->manual_tax_amount ?? 0, ['class' => 'form-control', 'placeholder' => __('Adjusted Tax')]) !!}
          </div>
        </div>
      </div>
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
    let subtotal = parseFloat($('[name="subtotal"]').val());

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
</script>
