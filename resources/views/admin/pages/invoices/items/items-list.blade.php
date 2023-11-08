<div class="col-12">
  <div class="table-responsive m-t-40 invoice-table-wrapper editing clear-both">
      <table class="table table-hover invoice-table editing">
      <button type="button" class="btn btn-primary btn-sm float-end me-2 select-items-btn">Select Items</button>
      <button type="button" class="btn btn-primary btn-sm float-end me-2 d-none delete-items-btn">Delete Selected</button>
      @if ($is_editable)
        @if($invoice->type == 'Regular')
          <button type="button" class="btn btn-primary btn-sm float-end me-2" data-title="{{__('Add Phases')}}" data-toggle='ajax-modal' data-href="{{route('admin.invoices.invoice-items.create',[$invoice])}}">Add Phases</button>
        @else
          <button type="button" class="btn btn-primary btn-sm float-end me-2" data-title="{{__('Add Item')}}" data-toggle='ajax-modal' data-href="{{route('admin.invoices.custom-invoice-items.create',[$invoice])}}">Add Item</button>
        @endif
      @endif
          <thead>
              <tr>
                  <!--action-->
                  @if ($is_editable)
                    <th class="text-left x-action bill_col_action"><input type="checkbox" class="form-check-input select-all-items d-none"> Action</th>
                  @endif
                  <!--description-->
                  <th class="text-left x-description bill_col_description">Item</th>
                  <th class="text-left x-description bill_col_description">Price</th>
                  <th class="text-left x-description bill_col_description">QTY</th>
                  <th class="text-left x-rate bill_col_rate">Subtotal</th>
                  <th class="text-left">Down Payment</th>
                  <!--tax-->
                  @if (!$invoice->is_summary_tax)
                    <th class="" style="min-width: 180px;">Tax</th>
                  @endif
                  <!--total-->
                  <th class="text-right x-total bill_col_total" id="bill_col_total">Total</th>
              </tr>
          </thead>
          <tbody id="billing-items-container" class="billing-items-container-editing">
            @include('admin.pages.invoices.items.edit-list')
          </tbody>
      </table>
  </div>
</div>
