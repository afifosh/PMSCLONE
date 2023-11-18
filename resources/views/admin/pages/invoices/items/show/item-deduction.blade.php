<tr data-id="{{$item->id}}">
  <td>
    <a data-toggle="ajax-delete" data-href="{{ route('admin.invoices.invoice-items.deductions.destroy', [$invoice, 'invoice_item' => $item->id, $item->deduction->id]) }}"><i class="ti ti-trash"></i></a>
  </td>
  <!--description-->
  <td>
    Down Payment Deduction
    @if($item->deduction->is_percentage)
      ({{$item->deduction->amount}}%)
    @endif
  </td>
  <td
    @if($item->deduction->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{cMoney(-($item->deduction?->amount ?? 0), $invoice->contract->currency, true)}}"
    @endif
  >
    @cMoney(-($item->deduction->manual_amount ? $item->deduction->manual_amount : $item->deduction?->amount ?? 0), $invoice->contract->currency, true)
    @if($item->deduction->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
  <td></td>
  <td
    @if($item->deduction->manual_amount)
        data-bs-toggle="tooltip" title="Calculated amount: {{cMoney(-($item->deduction?->amount ?? 0), $invoice->contract->currency, true)}}"
    @endif
  >
    @cMoney(-($item->deduction->manual_amount ? $item->deduction->manual_amount : $item->deduction?->amount ?? 0), $invoice->contract->currency, true)
    @if($item->deduction->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>

  <!--total-->
  <td class="text-right"
    @if($item->deduction->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{cMoney(-($item->deduction?->amount ?? 0), $invoice->contract->currency, true)}}"
    @endif
    >
    @cMoney(-($item->deduction->manual_amount ? $item->deduction->manual_amount : $item->deduction?->amount ?? 0), $invoice->contract->currency, true)
    @if($item->deduction->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
</tr>
