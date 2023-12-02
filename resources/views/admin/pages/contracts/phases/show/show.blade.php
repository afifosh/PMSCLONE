<div class="source-item pt-0 px-0 px-sm-4">
    <div class="col-12">
        <div class="table-responsive m-t-40 invoice-table-wrapper editing clear-both">
            <table class="table table-hover invoice-table editing" id="billing-items-container">
                <thead data-id="exclude-sort" id="billing-items-container-header">
                    <tr>
                        <!--action-->
                        <th class="text-left x-action bill_col_action"><input type="checkbox"
                                class="form-check-input select-all-items d-none"> Action</th>
                        <!--description-->
                        <th class="text-left x-description bill_col_description">Item</th>
                        <th class="x-description bill_col_description text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @includeWhen($phase->deduction && $phase->deduction->is_before_tax ,'admin.pages.contracts.phases.show.deduction')
                    {{--  --}}
                    @if (@$phase->deduction && @$phase->deduction->is_before_tax)
                      <tr style="background-color: #efeff163;">
                        <td>Subtotal</td>
                        <td></td>
                        <td class="text-end">
                            @cMoney($phase->estimated_cost - ($phase->deduction->manual_amount ? @$phase->deduction->manual_amount : ($phase->deduction->amount ?? 0)), $phase->contract->currency, true)
                        </td>
                      </tr>
                    @endif
                    {{--  --}}
                    @include('admin.pages.contracts.phases.show.taxes')
                    {{--  --}}
                    @if(@$phase->deduction && !@$phase->deduction->is_before_tax)
                      <tr style="background-color: #efeff163;">
                        <td>Subtotal</td>
                        <td></td>
                        <td class="text-end">
                            @cMoney($phase->total_cost - ($phase->deduction->manual_amount ? @$phase->deduction->manual_amount : ($phase->deduction->amount ?? 0)), $phase->contract->currency, true)
                        </td>
                      </tr>
                    @endif
                    {{--  --}}
                    @includeWhen($phase->deduction && !$phase->deduction->is_before_tax,'admin.pages.contracts.phases.show.deduction')
                    <tr style="background-color: #efeff1;">
                        <td>Item Total</td>
                        <td></td>
                        <td class="text-end">@cMoney($phase->total_cost, $phase->contract->currency, true)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
