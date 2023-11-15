{{-- Name --}}
<div class="form-group col-6">
  {{ Form::label('name', __('Item Name'), ['class' => 'col-form-label']) }}
  {!! Form::text('name', @$invoiceItem->invoiceable->name, ['class' => 'form-control', $disabled,  'placeholder' => __('Item Name')]) !!}
</div>
{{-- price --}}
<div class="form-group col-6">
    {{ Form::label('price', __('Price'), ['class' => 'col-form-label']) }}
    {!! Form::number('price', @$invoiceItem->invoiceable->price, ['class' => 'form-control', $disabled, 'placeholder' => __('Price')]) !!}
</div>
{{-- quantity --}}
<div class="form-group col-6">
    {{ Form::label('quantity', __('Quantity'), ['class' => 'col-form-label']) }}
    {!! Form::number('quantity', @$invoiceItem->invoiceable->quantity, ['class' => 'form-control', $disabled, 'placeholder' => __('Quantity')]) !!}
</div>
