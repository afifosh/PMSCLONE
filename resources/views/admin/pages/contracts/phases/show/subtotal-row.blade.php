<tr style="background-color: #efeff163;">
  <td>
    <a onclick="editPhaseSubtotalAmount({{$phase->id}}, this)"><i class="ti ti-pencil"></i></a>
  </td>
  <td class="fw-bold">Subtotall</td>
  <td class="text-end fw-bold"
    @if($phase->subtotal_amount_adjustment)
      data-bs-toggle="tooltip" title="Calculated amount: {{cMoney($phase->subtotal_row_raw, $phase->contract->currency, true)}}"
    @endif
  >
      @cMoney($phase->subtotal_row, $phase->contract->currency, true)
      @if($phase->subtotal_amount_adjustment)
        <span class="text-danger">*</span>
      @endif
  </td>
</tr>
