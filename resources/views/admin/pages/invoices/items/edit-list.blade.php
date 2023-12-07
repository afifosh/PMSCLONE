@forelse ($invoice->items as $item)
@continue($tab == 'tax-report' && count($item->taxes->where(function($tax){
  return $tax->pivot->category == 1;
 })) == 0)
@continue($invoice->type == 'Partial Invoice' && $item->invoiceable_type == 'App\Models\ContractPhase')
<tbody data-id="{{$item->id}}">
  <tr data-id="{{$item->id}}">
    @if ($is_editable && $tab != 'tax-report')
      <!--action-->
      <td class="cursor-pointer">
        {!! Form::checkbox('selected_phases[]', $item->id, null, ['class' => 'form-check-input mt-1 d-none']) !!}

        <span class="bi-drag pt-1 cursor-grab"><i class="ti ti-menu-2"></i></span>
        <a data-toggle="ajax-modal" data-size='modal-xl' data-title="{{__('Edit Item')}}" data-href="{{route('admin.invoices.invoice-items.edit', [$invoice,'invoice_item' => $item->id, 'tab' => $tab, 'type' => 'edit-form'])}}">
          <i class="ti ti-pencil"></i></a>
      </td>
    @endif

    <!--description-->
    <td>{{$item->invoiceable->name ?? runtimeInvIdFormat($item->invoiceable_id)}}</td>
    <td>
      @if ($item->invoiceable_type == 'App\Models\CustomInvoiceItem')
        @cMoney($item->invoiceable->price, $invoice->contract->currency, true)
      @else
        @cMoney($item->subtotal, $invoice->contract->currency, true)
      @endif
    </td>
    <td>{{$item->invoiceable->quantity ?? 1}}</td>
    <td>@cMoney($item->subtotal, $invoice->contract->currency, true)</td>
    <!--total-->
    <td class="text-end">@cMoney($item->subtotal, $invoice->contract->currency, true)</td>
  @includeWhen(@$item->deduction && @$item->deduction->is_before_tax, 'admin.pages.invoices.items.show.item-deduction')
  {{-- item subtoital if deduction is before tax --}}
  @if (@$item->deduction && @$item->deduction->is_before_tax)
    <tr style="background-color: #efeff163;">
      @if($invoice->isEditable() && $tab != 'tax-report')
        <td></td>
      @endif
      <td>Subtotal</td>
      <td></td>
      <td></td>
      <td></td>
      <td class="text-end">
        @if($tab != 'tax-report')
          @cMoney($item->subtotal - ($item->deduction->manual_amount ? @$item->deduction->manual_amount : ($item->deduction->amount ?? 0)), $invoice->contract->currency, true)
        @endif
      </td>
    </tr>

  @endif
  @includeWhen(count($item->taxes) > 0, 'admin.pages.invoices.items.show.item-tax')

  {{-- item subtotal if deduction after tax --}}
  @if($tab != 'authority-tax' && @$item->deduction && !@$item->deduction->is_before_tax)
    <tr style="background-color: #efeff163;">
      @if($invoice->isEditable() && $tab != 'tax-report')
        <td></td>
      @endif
      <td>Subtotal</td>
      <td></td>
      <td></td>
      <td></td>
      <td class="text-end">
        @if($tab != 'tax-report')
        @cMoney($item->total + ($item->deduction->manual_amount ? @$item->deduction->manual_amount : ($item->deduction->amount ?? 0)), $invoice->contract->currency, true)
        @endif
      </td>
    </tr>
  @endif
  @includeWhen($tab != 'authority-tax' && @$item->deduction && !@$item->deduction->is_before_tax, 'admin.pages.invoices.items.show.item-deduction')
  <tr style="background-color: #efeff1;">
    @if($invoice->isEditable() && $tab != 'tax-report')
      <td></td>
    @endif
    <td>Item Total</td>
    <td></td>
    <td></td>
    <td></td>
    <td class="text-end">
      @if($tab != 'tax-report')
        @cMoney($tab == 'summary' ? $item->total : $item->authority_inv_total, $invoice->contract->currency, true)
      @endif
    </td>
  </tr>
</tbody>
@empty
@endforelse
