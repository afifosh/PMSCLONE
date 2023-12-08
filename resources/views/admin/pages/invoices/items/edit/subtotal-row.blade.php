<tr style="background-color: #efeff163;">
  @if($invoice->isEditable())
    <td>
      <a onclick="editItemSubtotalAmount({{$item->id}}, this)"><i class="ti ti-pencil"></i></a>
    </td>
  @endif
  <td class="fw-bold">Subtotal</td>
  <td></td>
  <td></td>
  <td></td>
  <td class="text-end fw-bold"
    @if($tab != 'tax-report' && $item->subtotal_amount_adjustment)
      data-bs-toggle="tooltip" title="Calculated amount: {{cMoney($item->subtotal_row_raw, $invoice->contract->currency, true)}}"
    @endif
    >
    @if($tab != 'tax-report')
      @cMoney($item->subtotal_row, $invoice->contract->currency, true)
      @if($item->subtotal_amount_adjustment)
        <span class="text-danger">*</span>
      @endif
    @endif
  </td>
</tr>
