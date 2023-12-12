@forelse ($taxes as $tax)
<tr>
  <!--description-->
  <td>
    {{$tax->tax->name}}
    ({{$tax->tax->categoryName($tax->category)}})
  </td>
  <td
    @if($tax->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{$tax->category == 2 && $tab == 'summary' ? '-' : ''}} {{cMoney(($tax->calculated_amount), $invoice->contract->currency, true)}}"
    @endif
    >
    {{$tax->category == 2 && $tab == 'summary' ? '-' : ''}} @cMoney(($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount), $invoice->contract->currency, true)
    @if($tax->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
  <td></td>
  <td
    @if($tax->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{$tax->category == 2 && $tab == 'summary' ? '-' : ''}} {{cMoney(($tax->calculated_amount), $invoice->contract->currency, true)}}"
    @endif
    >
    {{$tax->category == 2 && $tab == 'summary' ? '-' : ''}}@cMoney(($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount), $invoice->contract->currency, true)
    @if($tax->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>

  <!--total-->
  <td class="text-end"
    @if($tax->manual_amount)
      data-bs-toggle="tooltip" title="Calculated amount: {{$tax->category == 2 && $tab == 'summary' ? '-' : ''}} {{cMoney(($tax->calculated_amount), $invoice->contract->currency, true)}}"
    @endif
    >
    {{$tax->category == 2 && $tab == 'summary' ? '-' : ''}}@cMoney(($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount), $invoice->contract->currency, true)
    @if($tax->manual_amount)
      <span class="text-danger">*</span>
    @endif
  </td>
</tr>
@empty
@endforelse
