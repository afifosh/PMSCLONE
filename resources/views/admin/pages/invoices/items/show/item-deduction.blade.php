<tr data-id="{{$item->id}}">
  <td></td>
  <!--description-->
  <td>Down Payment</td>
  <td>
    @cMoney(-($item->deduction->manual_amount ? $item->deduction->manual_amount : $item->deduction?->amount ?? 0), $invoice->contract->currency, true)
    @if($item->deduction->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
  <td></td>
  <td>
    @cMoney(-($item->deduction->manual_amount ? $item->deduction->manual_amount : $item->deduction?->amount ?? 0), $invoice->contract->currency, true)
    @if($item->deduction->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>

  <!--total-->
  <td class="text-right">
    @cMoney(-($item->deduction->manual_amount ? $item->deduction->manual_amount : $item->deduction?->amount ?? 0), $invoice->contract->currency, true)
    @if($item->deduction->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
</tr>
