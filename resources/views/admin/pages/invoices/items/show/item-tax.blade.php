@forelse ($item->taxes as $tax)
  @continue($tab == 'authority-tax' && !$tax->pivot->is_authority_tax)
  @continue($tab == 'summary' && !$tax->pivot->is_simple_tax)
  @continue($tab == 'tax-report' && !$tax->pivot->is_simple_tax)
<tr data-id="{{$item->id}}">
  @if($tab != 'tax-report')
    <td>
      <a data-toggle="ajax-delete" data-href="{{ route('admin.invoices.invoice-items.taxes.destroy', [$invoice, 'invoice_item' => $item->id, 'tax' => $tax->pivot->id]) }}"><i class="ti ti-trash"></i></a>
    </td>
  @endif
  <!--description-->
  <td>{{$tax->name}}</td>
  <td>
    {{$tax->pivot->pay_on_behalf && $tab == 'summary' ? '-' : ''}} @cMoney(($tax->pivot->manual_amount ? $tax->pivot->manual_amount : $tax->pivot->calculated_amount) / 1000, $invoice->contract->currency, true)
    @if($tax->pivot->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
  <td></td>
  <td>
    {{$tax->pivot->pay_on_behalf && $tab == 'summary' ? '-' : ''}}@cMoney(($tax->pivot->manual_amount ? $tax->pivot->manual_amount : $tax->pivot->calculated_amount) / 1000, $invoice->contract->currency, true)
    @if($tax->pivot->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>

  <!--total-->
  <td class="text-right">
    {{$tax->pivot->pay_on_behalf && $tab == 'summary' ? '-' : ''}}@cMoney(($tax->pivot->manual_amount ? $tax->pivot->manual_amount : $tax->pivot->calculated_amount) / 1000, $invoice->contract->currency, true)
    @if($tax->pivot->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
</tr>
@empty
@endforelse
