<tr class="expanded-edit-row" style="background-color: var(--bs-light)">
  <td colspan="6">
    @if ($invoiceItem->id)
        {{-- item update --}}
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
          <div class="row">
            @php
              $disabled = isset($tax_rates) || isset($deduction_rates) ? 'disabled' : '';
            @endphp
            @includeWhen((request()->item != 'custom'), 'admin.pages.invoices.items.edit-phases-general')
            @includeWhen((request()->item == 'custom'), 'admin.pages.invoices.items.edit-custom-item-general')
            @if(isset($deduction_rates))
              {{-- Subtotal --}}
              <div class="form-group col-6">
                {{ Form::label('subtotal', __('Subtotal'), ['class' => 'col-form-label']) }}
                {!! Form::number('subtotal', null, ['class' => 'form-control', $disabled,'placeholder' => __('Subtotal')]) !!}
              </div>
            @endif
          </div>
          @if(isset($deduction_rates))
            <div class="row">
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
              {{-- decription --}}
              <div class="form-group col-12">
                {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
                {!! Form::text('description', null, ['class' => 'form-control', $disabled, 'placeholder' => __('Description')]) !!}
              </div>
            </div>
          @endif
    </div>

    <div class="mt-3">
        <div class="btn-flt float-end">
            <button type="button" class="btn btn-secondary" onclick="$(this).closest('tr').remove()">{{ __('Close') }}</button>
            <button type="submit" data-form="ajax-form" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </div>
    {!! Form::close() !!}
  </td>
</tr>
