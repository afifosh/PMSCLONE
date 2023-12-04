<tr class="expanded-edit-row" style="background-color: #efb7c461">
  <td colspan="6">
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
          {{-- Include Texes Section--}}
          @includeWhen(isset($tax_rates), 'admin.pages.invoices.items.edit.edit-tax')
          {{-- @include('admin.pages.invoices.items.edit.edit-tax') --}}

          {{-- Deduction --}}
          @if(isset($deduction_rates) && $invoice->type != 'Down Payment' && count($invoice->deductableDownpayments ?? []) > 0)
            @include('admin.pages.invoices.items.edit-deduction')
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
