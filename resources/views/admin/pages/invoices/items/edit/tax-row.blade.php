@forelse ($item->taxes as $tax)
  @continue($tab == 'authority-tax' && $tax->pivot->category == 1)
  @continue($tab == 'summary' && $tax->pivot->category == 3)
  @continue($tab == 'tax-report' && $tax->pivot->category == 3)
<tr data-id="{{$item->id}}">
  @if($tab != 'tax-report' && $is_editable)
    <td>
      <a data-toggle="ajax-delete" data-href="{{ route('admin.invoices.invoice-items.taxes.destroy', [$invoice, 'invoice_item' => $item->id, 'tax' => $tax->pivot->id]) }}"><i class="ti ti-trash"></i></a>
      <a data-toggle="ajax-modal" onclick="editItemTax({{$item->invoice_id}}, {{$item->id}}, {{$tax->pivot->id}}, this)"><i class="ti ti-pencil"></i></a>
    </td>
  @endif
  <!--description-->
  <td>
    {{$tax->name}}
    ({{$tax->categoryName($tax->pivot->category)}})
  </td>
  <td
    @if($tax->pivot->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{$tax->pivot->category == 2 && $tab == 'summary' ? '-' : ''}} {{cMoney(($tax->pivot->calculated_amount / 1000), $invoice->contract->currency, true)}}"
    @endif
    >
    {{$tax->pivot->category == 2 && $tab == 'summary' ? '-' : ''}} @cMoney(($tax->pivot->manual_amount ? $tax->pivot->manual_amount : $tax->pivot->calculated_amount) / 1000, $invoice->contract->currency, true)
    @if($tax->pivot->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
  <td></td>
  <td
    @if($tax->pivot->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{$tax->pivot->category == 2 && $tab == 'summary' ? '-' : ''}} {{cMoney(($tax->pivot->calculated_amount / 1000), $invoice->contract->currency, true)}}"
    @endif
    >
    {{$tax->pivot->category == 2 && $tab == 'summary' ? '-' : ''}}@cMoney(($tax->pivot->manual_amount ? $tax->pivot->manual_amount : $tax->pivot->calculated_amount) / 1000, $invoice->contract->currency, true)
    @if($tax->pivot->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>

  <!--total-->
  <td class="text-end"
    @if($tax->pivot->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{$tax->pivot->category == 2 && $tab == 'summary' ? '-' : ''}} {{cMoney(($tax->pivot->calculated_amount / 1000), $invoice->contract->currency, true)}}"
    @endif
    >
    {{$tax->pivot->category == 2 && $tab == 'summary' ? '-' : ''}}@cMoney(($tax->pivot->manual_amount ? $tax->pivot->manual_amount : $tax->pivot->calculated_amount) / 1000, $invoice->contract->currency, true)
    @if($tax->pivot->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
</tr>
@empty
@endforelse