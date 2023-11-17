@forelse ($invoice->items as $item)
@continue($tab == 'tax-report' && count($item->taxes->where(function($tax){
  return $tax->pivot->is_simple_tax == 1;
 })) == 0)
@continue($invoice->type == 'Partial Invoice' && $item->invoiceable_type == 'App\Models\ContractPhase')
<tbody data-id="{{$item->id}}">
  <tr data-id="{{$item->id}}">
    @if ($is_editable && $tab != 'tax-report')
      <!--action-->
      <td class="cursor-pointer">
        {!! Form::checkbox('selected_phases[]', $item->id, null, ['class' => 'form-check-input mt-1 d-none']) !!}

        <span class="bi-drag pt-1 cursor-grab"><i class="ti ti-menu-2"></i></span>
        <a class="dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
          <i class="ti ti-pencil"></i></a>
        <div class="dropdown-menu dropdown-menu-end m-0">
            @if ($item->invoiceable_type == 'App\Models\CustomInvoiceItem')
              <li class="dropdown-item" data-toggle="ajax-modal" data-title="{{__('Edit Item')}}" data-href="{{route('admin.invoices.invoice-items.edit', [$invoice,'invoice_item' => $item->id, 'tab' => $tab])}}">Edit</i></li>
            @elseif ($item->invoiceable_type == 'App\Models\ContractPhase')
              <li class="dropdown-item" data-toggle="ajax-modal" data-title="{{__('Edit Phase')}}" data-href="{{route('admin.invoices.invoice-items.edit', [$invoice,'invoice_item' => $item->id, 'tab' => $tab])}}">Edit</i></li>
            @endif
            <li class="dropdown-item" data-toggle="ajax-modal" data-title="{{__('Add Tax')}}" data-href="{{route('admin.invoices.invoice-items.taxes.create', [$invoice, 'invoice_item' => $item->id, 'tab' => $tab])}}">Add Tax</li>
            @if(!$item->deduction)
              <li class="dropdown-item" data-toggle="ajax-modal" data-title="{{__('Add Deduction')}}" data-href="{{route('admin.invoices.invoice-items.deductions.create', [$invoice, 'invoice_item' => $item->id, 'tab' => $tab])}}">Add Deduction</li>
            @endif
        </div>
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
    <td class="text-right">@cMoney($item->subtotal, $invoice->contract->currency, true)</td>
  @includeWhen(@$item->deduction && @$item->deduction->is_before_tax, 'admin.pages.invoices.items.show.item-deduction')
  @includeWhen(count($item->taxes) > 0, 'admin.pages.invoices.items.show.item-tax')
  @includeWhen($tab != 'authority-tax' && @$item->deduction && !@$item->deduction->is_before_tax, 'admin.pages.invoices.items.show.item-deduction')
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>
      @if($tab != 'tax-report')
        @cMoney($tab == 'summary' ? $item->total : $item->authority_inv_total, $invoice->contract->currency, true)
      @endif
    </td>
  </tr>
</tbody>
@empty
@endforelse
