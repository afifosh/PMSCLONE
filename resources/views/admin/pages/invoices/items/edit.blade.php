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
      {{-- Downpayment Deduction --}}
      <div class="form-group col-6">
        {{ Form::label('downpayment_id', __(' Downpayment'), ['class' => 'col-form-label']) }}
        <select name="downpayment_id" id="downpayment_id" class="form-select globalOfSelect2">
          <option value="">{{__('Select Down payment')}}</option>
          @forelse ($invoice->deductableDownpayments as $dp)
            <option data-amount="{{$dp->total}}" value="{{$dp->id}}">{{runtimeInvIdFormat($dp->id)}} ( Total: @cMoney($dp->total, $invoice->contract->currency, true) )</option>
          @empty
          @endforelse
        </select>
      </div>
      {{-- Downpayment Deduction --}}
      <div class="form-group col-6 d-none">
        {{ Form::label('downpayment_amount', __('Downpayment Amount'), ['class' => 'col-form-label']) }}
        {!! Form::number('downpayment_amount', 0, ['class' => 'form-control','placeholder' => __('Downpayment Amount')]) !!}
      </div>
      {{-- Taxes --}}
      <div class="form-group col-6">
        {{ Form::label('item_taxes', __('Tax'), ['class' => 'col-form-label']) }}
        <select class="form-select globalOfSelect2" name="item_taxes[]" multiple data-placeholder="{{__('Select Tax')}}">
          @forelse ($tax_rates->where('is_retention', false) as $tax)
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
      {{-- Total --}}
      <div class="form-group col-6">
        {{ Form::label('total', __('Total'), ['class' => 'col-form-label']) }}
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
  // calcualte total tax
  $(document).on('change', '#item-create [name="item_taxes[]"]', function(){
    calculateCustomItemValues();
  })
  // calcualte total, quantity * price
  $(document).on('change keyup', '[name="quantity"], [name="price"]', function(){
    calculateCustomItemValues();
  })

  // toggle manual tax
  $(document).on('change', '#item-create [name="is_manual_tax"]', function(){
    if($(this).is(':checked')){
      $('#item-create [name="manual_tax_amount"]').parent().removeClass('d-none');
    }else{
      $('#item-create [name="manual_tax_amount"]').parent().addClass('d-none');
    }
    calculateCustomItemValues();
  })

  // onchange manual tax amount recalculate total
  $(document).on('change keyup', '#item-create [name="manual_tax_amount"]', function(){
    calculateCustomItemValues();
  })

  // toggle downpayment deduction
  $(document).on('change', '#item-create [name="downpayment_id"]', function(){
    if($(this).val()){
      $('#item-create [name="downpayment_amount"]').parent().removeClass('d-none');
    }else{
      $('#item-create [name="downpayment_amount"]').parent().addClass('d-none');
    }
    calculateCustomItemValues();
  })

  function calculateCustomItemValues (){
    let subtotal = parseFloat($('#item-create [name="subtotal"]').val());

    // downpayment amount
    if($('#item-create [name="downpayment_id"]').val()){
      subtotal -= parseFloat($('#item-create [name="downpayment_amount"]').val());
    }


    let totalTax = 0;
    // calculate total tax
    var taxes = $('#item-create [name="item_taxes[]"]').val();
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

    // total amount
    let totalAmount = subtotal + totalTax
    // if manual tax is checked, use the manual tax amount
    if($('#item-create [name="is_manual_tax"]').is(':checked')){
      const manualTax = parseFloat($('#item-create [name="manual_tax_amount"]').val());
      totalAmount = subtotal + manualTax;
    }

    // set total tax and total amount
    $('#item-create [name="total_tax_amount"]').val(totalTax.toFixed(3));
    $('#item-create [name="total"]').val(totalAmount.toFixed(3));
  }
</script>
