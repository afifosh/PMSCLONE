@forelse ($invoice->items as $item)
@continue($invoice->type == 'Partial Invoice' && $item->invoiceable_type == 'App\Models\ContractPhase')
<tr data-id="{{$item->id}}">
  @if ($is_editable)
    <!--action-->
    <td class="cursor-pointer">
      {!! Form::checkbox('selected_phases[]', $item->id, null, ['class' => 'form-check-input mt-1 d-none']) !!}

      <span class="bi-drag pt-1 cursor-grab"><i class="ti ti-menu-2"></i></span>
      {{-- <span data-toggle="ajax-delete" data-href={{route('admin.invoices.invoice-items.destroy', [$invoice,'invoice_item' => $item->id])}}><i class="ti ti-trash"></i> </span> --}}
    </td>
  @endif

  <!--description-->
  <td class="">{{$item->invoiceable->name ?? runtimeInvIdFormat($item->invoiceable_id)}}</td>
  <td>
    @if ($item->invoiceable_type == 'App\Models\CustomInvoiceItem')
      @cMoney($item->invoiceable->price, $invoice->contract->currency, true)
    @else
      @cMoney($item->amount, $invoice->contract->currency, true)
    @endif
  </td>
  <td>{{$item->invoiceable->quantity ?? 1}}</td>
  <td class="">@cMoney($item->amount, $invoice->contract->currency, true)</td>
  <!--tax-->
  @if (!$invoice->is_summary_tax)
    <td class="text-left" style="max-width: 170px;">
      <div class="mb-3">
        <select {{$is_editable ? ($invoice->type != 'Partial Invoice' ? 'disabled' : '') : 'disabled'}} class="form-select invoice_taxes select2" data-item-id="{{$item->id}}" name="invoice_taxes[]" multiple data-placeholder="{{__('Select Tax')}}">
          @forelse ($tax_rates->where('is_retention', false) as $tax)
            <option @selected($item->taxes->contains($tax)) value="{{$tax->id}}">{{$tax->name}} (
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
    </td>
  @endif
  <!--total-->
  <td class="text-right x-total bill_col_total" id="bill_col_total">@cMoney($item->amount + $item->total_tax_amount, $invoice->contract->currency, true)
  </td>
</tr>
@empty
@endforelse
