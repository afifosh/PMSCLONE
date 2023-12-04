<tr data-id="{{$item->id}}">
  @if($tab != 'tax-report')
    <td>
      <a data-toggle="ajax-delete" data-href="{{ route('admin.invoices.invoice-items.deductions.destroy', [$invoice, 'invoice_item' => $item->id, $item->deduction->id]) }}"><i class="ti ti-trash"></i></a>
      <a data-toggle="ajax-modal" onclick="editItemDeduction({{$item->invoice_id}}, {{$item->id}}, {{$item->deduction->id}}, this)"><i class="ti ti-pencil"></i></a>
    </td>
  @endif
  <!--description-->
  <td>
    Down Payment Deduction
    @if($item->deduction->is_percentage)
      ({{$item->deduction->percentage}}%)
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
  <td class="text-end"
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
