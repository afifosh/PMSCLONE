<tr data-id="{{$item->id}}">
  <td>
    <a data-toggle="ajax-delete" data-href="{{ route('admin.invoices.invoice-items.deductions.destroy', [$invoice, 'invoice_item' => $item->id, $item->deduction->id]) }}"><i class="ti ti-trash"></i></a>
  </td>
  <!--description-->
  <td>Down Payment Deduction</td>
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
