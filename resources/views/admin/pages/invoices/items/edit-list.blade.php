@forelse ($invoice->items as $item)
<tr>
  <!--action-->
  <td class="text-left x-action bill_col_action" data-toggle="ajax-delete" data-href={{route('admin.invoices.invoice-items.destroy', [$invoice,'invoice_item' => $item->id])}}><i class="ti ti-trash"></i> </td>
  <!--description-->
  <td class="text-left x-description bill_col_description">{{$item->invoiceable->name}}
  </td>
  <td class="text-left x-rate bill_col_rate">@money($item->amount, $invoice->contract->currency, true)</td>
  <!--tax-->
  @if (!$invoice->is_summary_tax)
    <td class="text-left" style="max-width: 170px;">
      <div class="mb-3">
        <select class="form-select invoice_taxes select2" data-item-id="{{$item->id}}" name="invoice_taxes[]" multiple data-placeholder="{{__('Select Tax')}}">
          @forelse ($tax_rates as $tax)
            <option @selected($item->taxes->contains($tax)) value="{{$tax->id}}">{{$tax->name}} (
              @if($tax->type != 'Percent')
                @money($tax->amount, $invoice->contract->currency, true)
              @else
                {{$tax->amount}}%
              @endif
            )</option>
          @empty
          @endforelse
        </select>
      </div>
    </td>
  @endif
  <!--total-->
  <td class="text-right x-total bill_col_total" id="bill_col_total">@money($item->amount + $item->total_tax_amount, $invoice->contract->currency, true)
  </td>
</tr>
@empty
@endforelse
