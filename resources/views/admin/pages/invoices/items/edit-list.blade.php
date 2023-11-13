@forelse ($invoice->items as $item)
@continue($invoice->type == 'Partial Invoice' && $item->invoiceable_type == 'App\Models\ContractPhase')
<tbody data-id="{{$item->id}}">
  <tr data-id="{{$item->id}}">
    @if ($is_editable)
      <!--action-->
      <td class="cursor-pointer">
        {!! Form::checkbox('selected_phases[]', $item->id, null, ['class' => 'form-check-input mt-1 d-none']) !!}

        <span class="bi-drag pt-1 cursor-grab"><i class="ti ti-menu-2"></i></span>
        <a class="dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
          <i class="ti ti-pencil"></i></a>
        <div class="dropdown-menu dropdown-menu-end m-0">
            @if ($item->invoiceable_type == 'App\Models\CustomInvoiceItem')
              <li class="dropdown-item" data-toggle="ajax-modal" data-title="{{__('Edit Item')}}" data-href="{{route('admin.invoices.invoice-items.edit', [$invoice,'invoice_item' => $item->id])}}">Edit</i></li>
            @elseif ($item->invoiceable_type == 'App\Models\ContractPhase')
              <li class="dropdown-item" data-toggle="ajax-modal" data-title="{{__('Edit Phase')}}" data-href="{{route('admin.invoices.invoice-items.edit', [$invoice,'invoice_item' => $item->id])}}">Edit</i></li>
            @endif
            <li href="javascript:;" class="dropdown-item">Edit Tax</li>
            <li href="javascript:;" class="dropdown-item">Edit Deduction</li>
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
  @includeWhen($item->total_tax_amount != 0, 'admin.pages.invoices.items.show.item-tax')
  @includeWhen(@$item->deduction && !@$item->deduction->is_before_tax, 'admin.pages.invoices.items.show.item-deduction')
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>@cMoney($item->total, $invoice->contract->currency, true)</td>
  </tr>
</tbody>
@empty
@endforelse
