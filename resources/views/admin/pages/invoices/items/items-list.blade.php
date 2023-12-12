<div class="col-12">
  <div class="table-responsive m-t-40 invoice-table-wrapper editing clear-both">
      <table class="table table-hover invoice-table editing" id="billing-items-container">
        @if($tab != 'tax-report')
          <button type="button" data-toggle="ajax-modal" data-title="Invoice Comments" data-href={{route('admin.contracts.invoices.comments.index', ['contract' => $invoice->contract_id, 'invoice' => $invoice->id, 'tab' => $tab])}} class="btn btn-primary btn-sm float-end me-2">Comments</button>
        @endif
      @if($invoice->type != 'Down Payment' && $tab == 'summary')
        <button type="button" class="btn btn-primary btn-sm float-end me-2 select-items-btn">Select Items</button>
        <button type="button" class="btn btn-primary btn-sm float-end me-2 d-none delete-items-btn">Delete Selected</button>
        @if ($is_editable)
          @if($invoice->type == 'Regular')
            <button type="button" class="btn btn-primary btn-sm float-end me-2" data-title="{{__('Add Phases')}}" data-toggle='ajax-modal' data-href="{{route('admin.invoices.invoice-items.create',[$invoice])}}">Add Phases</button>
          @else
            <button type="button" class="btn btn-primary btn-sm float-end me-2" data-title="{{__('Add Item')}}" data-toggle='ajax-modal' data-href="{{route('admin.invoices.invoice-items.create',[$invoice, 'item' => 'custom'])}}">Add Item</button>
          @endif
        @endif
      @endif
          <thead data-id="exclude-sort" id="billing-items-container-header">
              <tr>
                  <!--action-->
                  @if ($is_editable && $tab == 'summary')
                    <th class="text-left x-action bill_col_action"><input type="checkbox" class="form-check-input select-all-items d-none"> Action</th>
                  @endif
                  <!--description-->
                  <th class="text-left x-description bill_col_description">Item</th>
                  <th class="text-left x-description bill_col_description">Price</th>
                  <th class="text-left x-description bill_col_description">QTY</th>
                  <th class="text-left x-rate bill_col_rate">Subtotal</th>
                  <th class="text-end x-total bill_col_total" id="bill_col_total">Total</th>
              </tr>
          </thead>
          @if($tab == 'summary')
            @include('admin.pages.invoices.items.edit-list')
          @else
            @include('admin.pages.invoices.items.tax-report-list')
          @endif
      </table>
  </div>
</div>
