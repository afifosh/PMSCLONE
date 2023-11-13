<tr data-id="{{$item->id}}">
  <td></td>
  <!--description-->
  <td>Tax</td>
  <td>
    @cMoney(($item->manual_tax_amount ? $item->manual_tax_amount : $item->total_tax_amount), $invoice->contract->currency, true)
    @if($item->manual_tax_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
  <td></td>
  <td>
    @cMoney(($item->manual_tax_amount ? $item->manual_tax_amount : $item->total_tax_amount), $invoice->contract->currency, true)
    @if($item->manual_tax_amount)
      <span class="text-danger">*</span>
    @endif
  </td>

  <!--total-->
  <td class="text-right">
    @cMoney(($item->manual_tax_amount ? $item->manual_tax_amount : $item->total_tax_amount), $invoice->contract->currency, true)
    @if($item->manual_tax_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
</tr>
