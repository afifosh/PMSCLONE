@if ($customInvoiceItem->id)
    {!! Form::model($customInvoiceItem, ['route' => ['admin.invoices.custom-invoice-items.update', [$invoice, $customInvoiceItem->id]], 'method' => 'PUT']) !!}
@else
    {!! Form::model($customInvoiceItem, ['route' => ['admin.invoices.custom-invoice-items.store', $invoice], 'method' => 'POST']) !!}
@endif

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
      {{-- Total --}}
      <div class="form-group col-6">
          {{ Form::label('total', __('Total'), ['class' => 'col-form-label']) }}
          {!! Form::number('total', null, ['class' => 'form-control', 'disabled','placeholder' => __('Total')]) !!}
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
  // calcualte total, quantity * price
  $(document).on('change keyup', '[name="quantity"], [name="price"]', function(){
    var quantity = $('[name="quantity"]').val();
    var price = $('[name="price"]').val();
    var total = quantity * price;
    $('[name="total"]').val(total);
  })
</script>
