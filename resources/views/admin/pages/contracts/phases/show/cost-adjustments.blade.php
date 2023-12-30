@forelse ($phase->costAdjustments as $costAdjustment)
<tr>
  <!--description-->
  <td>
    @if($is_partial_paid)
      <span class="me-2">
        <a data-toggle="ajax-delete"
            data-href="{{route('admin.contracts.phases.cost-adjustments.destroy', ['contract' => $phase->contract_id, 'phase' => $phase->id, 'cost_adjustment' => $costAdjustment->id])}}"><i
                class="ti ti-trash"></i></a>
        <a onclick="editPhaseCostAdjustment({{$phase->id}}, {{$costAdjustment->id}}, this)"><i class="ti ti-pencil"></i></a>
      </span>
    @endif
    {{$costAdjustment->description}} ({{__('Adjustment')}})
  </td>
  <!--total-->
  <td class="text-end"
    >
    @cMoney($costAdjustment->amount, $phase->contract->currency, true)
  </td>
</tr>
@empty
@endforelse
