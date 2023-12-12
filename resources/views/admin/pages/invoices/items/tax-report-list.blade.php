<tbody>
  @if(count($invoice->allPivotTaxes->where('category', 1)) > 0 && $tab == 'tax-report')
    {{-- category 1 --}}
    <tr style="background-color: #efeff1;">
      <!--description-->
      <td>{{__('Value Added Tax')}}</td>
      <td>
        @cMoney($invoice->allPivotTaxes->where('category', 1)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
      <td>{{count($invoice->allPivotTaxes->where('category', 1))}}</td>
      <td></td>
      <!--total-->
      <td class="text-end">
        @cMoney($invoice->allPivotTaxes->where('category', 1)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
    </tr>
    @include('admin.pages.invoices.items.show.tax-report-row', ['taxes' => $invoice->allPivotTaxes->where('category', 1)])
  @endif

  @if(count($invoice->allPivotTaxes->where('category', 2)) > 0)
    {{-- category 2 --}}
    <tr style="background-color: #efeff1;">
      <!--description-->
      <td>{{__('Withholding Tax')}}</td>
      <td>
        @cMoney($invoice->allPivotTaxes->where('category', 2)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
      <td>{{count($invoice->allPivotTaxes->where('category', 2))}}</td>
      <td></td>
      <!--total-->
      <td class="text-end">
        @cMoney($invoice->allPivotTaxes->where('category', 2)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
    </tr>
    @include('admin.pages.invoices.items.show.tax-report-row', ['taxes' => $invoice->allPivotTaxes->where('category', 2)])
  @endif

  @if(count($invoice->allPivotTaxes->where('category', 3)) > 0)
    {{-- category 3 --}}
    <tr style="background-color: #efeff1;">
      <!--description-->
      <td>{{__('Reverse Charge')}}</td>
      <td>
        @cMoney($invoice->allPivotTaxes->where('category', 3)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
      <td>{{count($invoice->allPivotTaxes->where('category', 3))}}</td>
      <td></td>
      <!--total-->
      <td class="text-end">
        @cMoney($invoice->allPivotTaxes->where('category', 3)->sum(function($tax) use ($invoice) {
          return cMoney($tax->manual_amount ? $tax->manual_amount : $tax->calculated_amount, $invoice->contract->currency, true)->getAmount();
        }), $invoice->contract->currency, false)
      </td>
    </tr>
    @include('admin.pages.invoices.items.show.tax-report-row', ['taxes' => $invoice->allPivotTaxes->where('category', 3)])
  @endif
</tbody>
