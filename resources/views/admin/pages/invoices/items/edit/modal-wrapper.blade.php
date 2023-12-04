<div class="row invoice-item-edit-modal" data-item-id="{{$invoiceItem->id}}" data-invoice-id="{{$invoiceItem->invoice_id}}">
      <hr class="hr">
      <div id="item-edit-table-wrapper">
        @include('admin.pages.invoices.items.edit.table-wrapper', ['tab' => 'summary'])
      </div>
</div>

<div class="mt-3">
    <div class="btn-flt float-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
    </div>
</div>
{!! Form::close() !!}
