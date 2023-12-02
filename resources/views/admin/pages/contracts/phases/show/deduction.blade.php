<tr>
    <td>
      <a data-toggle="ajax-delete" data-href="{{route('admin.contracts.phases.deductions.destroy', ['contract' => $phase->contract_id, 'phase' => $phase->id, 'deduction' => $phase->deduction->id])}}"><i class="ti ti-trash"></i></a>
      <a data-toggle="ajax-modal" data-title="{{__('Edit Deduction')}}" data-href="{{route('admin.contracts.phases.deductions.edit', ['contract' => $phase->contract_id, 'phase' => $phase->id, 'deduction' => $phase->deduction->id])}}"><i class="ti ti-pencil"></i></a>
    </td>
  <!--description-->
  <td>
    Down Payment Deduction
    @if($phase->deduction->is_percentage)
      ({{$phase->deduction->percentage}}%)
    @endif
  </td>

  <!--total-->
  <td class="text-end"
    @if($phase->deduction->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{cMoney(-($phase->deduction?->amount ?? 0), $phase->contract->currency, true)}}"
    @endif
    >
    @cMoney(-($phase->deduction->manual_amount ? $phase->deduction->manual_amount : $phase->deduction?->amount ?? 0), $phase->contract->currency, true)
    @if($phase->deduction->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
</tr>
