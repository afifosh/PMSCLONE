@forelse ($phase->taxes as $tax)
<tr>
  <td>
      <a data-toggle="ajax-delete"
          data-href="{{route('admin.contracts.phases.taxes.destroy', ['contract' => $phase->contract_id, 'phase' => $phase->id, 'tax' => $tax->pivot->id])}}"><i
              class="ti ti-trash"></i></a>
      <a onclick="editPhaseTax({{$phase->contract_id}}, {{$phase->id}}, {{$tax->pivot->id}}, this)"><i class="ti ti-pencil"></i></a>
  </td>
  <!--description-->
  <td>
    {{$tax->name}}
    ({{$tax->categoryName($tax->pivot->category)}})
  </td>
  <!--total-->
  <td class="text-end"
    @if($tax->pivot->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{$tax->pivot->category == 2 ? '-' : ''}} {{cMoney(($tax->pivot->calculated_amount / 1000), $phase->contract->currency, true)}}"
    @endif
    >
    {{$tax->pivot->category == 2 ? '-' : ''}}@cMoney(($tax->pivot->manual_amount ? $tax->pivot->manual_amount : $tax->pivot->calculated_amount) / 1000, $phase->contract->currency, true)
    @if($tax->pivot->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
</tr>
@empty
@endforelse
