<div class="col-12">
  <div class="table-responsive m-t-40">
      <table class="table table-hover" id="billing-items-container">
        <button type="button" class="btn btn-primary btn-sm float-end me-2 test123" onclick="createItemtax({{$invoiceItem->invoice_id}}, {{$invoiceItem->id}}, this)">Add Tax</button>
        @if(!$invoiceItem->deduction && count($invoiceItem->invoice->deductableDownpayments) > 0)
          <button type="button" class="btn btn-primary btn-sm float-end me-2" onclick="createItemDeduction({{$invoiceItem->invoice_id}}, {{$invoiceItem->id}}, this)">Add Deduction</button>
        @endif
          <thead data-id="exclude-sort" id="billing-items-container-header">
              <tr>
                  <!--action-->
                  @if ($is_editable && $tab != 'tax-report')
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
          @include('admin.pages.invoices.items.edit.table-body', ['item' => $invoiceItem])
      </table>
  </div>
</div>
