<tbody>
  @php
      $pivotTaxes = $invoice->type == 'Regular' ? $pivotTaxes : $invoice->customItemsPivotTaxes;
  @endphp
  @if(count($pivotTaxes->where('category', 1)) > 0 && $tab == 'tax-report')
    {{-- category 1 --}}
    <tr style="background-color: #efeff1;">
      <!--description-->
      <td>{{__('Value Added Tax')}}</td>
      <td>
        @cMoney($pivotTaxes->where('category', 1)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
      <td>{{count($pivotTaxes->where('category', 1))}}</td>
      <td></td>
      <!--total-->
      <td class="text-end">
        @cMoney($pivotTaxes->where('category', 1)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
    </tr>
    @include('admin.pages.invoices.items.show.tax-report-row', ['taxes' => $pivotTaxes->where('category', 1)])
  @endif

  @if(count($pivotTaxes->where('category', 2)) > 0)
    {{-- category 2 --}}
    <tr style="background-color: #efeff1;">
      <!--description-->
      <td>{{__('Withholding Tax')}}</td>
      <td>
        @cMoney($pivotTaxes->where('category', 2)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
      <td>{{count($pivotTaxes->where('category', 2))}}</td>
      <td></td>
      <!--total-->
      <td class="text-end">
        @cMoney($pivotTaxes->where('category', 2)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
    </tr>
    @include('admin.pages.invoices.items.show.tax-report-row', ['taxes' => $pivotTaxes->where('category', 2)])
  @endif

  @if(count($pivotTaxes->where('category', 3)) > 0)
    {{-- category 3 --}}
    <tr style="background-color: #efeff1;">
      <!--description-->
      <td>{{__('Reverse Charge')}}</td>
      <td>
        @cMoney($pivotTaxes->where('category', 3)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
      <td>{{count($pivotTaxes->where('category', 3))}}</td>
      <td></td>
      <!--total-->
      <td class="text-end">
        @cMoney($pivotTaxes->where('category', 3)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
    </tr>
    @include('admin.pages.invoices.items.show.tax-report-row', ['taxes' => $pivotTaxes->where('category', 3)])
  @endif
</tbody>
