<div class="source-item pt-2">
    <div class="col-12">
      @if($phase->id)
        <div class="table-responsive m-t-40 invoice-table-wrapper editing clear-both">
          @if(!$is_paid)
            <button type="button" class="btn btn-primary btn-sm float-end me-2 waves-effect waves-light" onclick="createPhasetax({{$phase->id}}, this)">{{__('Add Tax')}}</button>
            @if(count(@$phase->contract->deductableDownpayments ?? []) > 0 && !$phase->deduction)
            <button type="button" class="btn btn-primary btn-sm float-end me-2 waves-effect waves-light" onclick="createPhaseDeduction({{$phase->id}}, this)">{{__('Add Deduction')}}</button>
            @endif
          @endif
          @if($is_partial_paid && $phase->is_allowable_cost)
            <button type="button" class="btn btn-primary btn-sm float-end me-2 waves-effect waves-light" onclick="createPhaseCostAdjustment({{$phase->id}}, this)">{{__('Add Adjustment')}}</button>
          @endif
            <table class="table table-hover invoice-table editing" id="billing-items-container">
                <thead data-id="exclude-sort" id="billing-items-container-header">
                    <tr>
                      @if(!$is_paid)
                        <!--action-->
                        <th class="text-left x-action bill_col_action"><input type="checkbox"
                                class="form-check-input select-all-items d-none"> Action</th>
                      @endif
                        <!--description-->
                        <th class="text-left x-description bill_col_description">Item</th>
                        <th class="x-description bill_col_description text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color: #efeff163;">
                      @if(!$is_paid)
                       <td><a onclick="editPhaseDetails({{$phase->id}}, this)"><i class="ti ti-pencil"></i></a></td>
                      @endif
                      <td>{{$phase->name}}</td>
                      <td class="text-end">
                          @cMoney($phase->estimated_cost, $phase->contract->currency, true)
                      </td>
                    </tr>
                    {{--  --}}
                    @includeWhen($phase->deduction && $phase->deduction->is_before_tax ,'admin.pages.contracts.phases.show.deduction')
                    {{--  --}}
                    @if (@$phase->deduction && @$phase->deduction->is_before_tax)
                      @include('admin.pages.contracts.phases.show.subtotal-row')
                    @endif
                    {{--  --}}
                    @include('admin.pages.contracts.phases.show.taxes')
                    {{--  --}}
                    @if(@$phase->deduction && !@$phase->deduction->is_before_tax)
                      @include('admin.pages.contracts.phases.show.subtotal-row')
                    @endif
                    {{--  --}}
                    @includeWhen($phase->deduction && !$phase->deduction->is_before_tax,'admin.pages.contracts.phases.show.deduction')
                    <tr style="background-color: #efeff1;">
                        @if(!$is_paid)
                          <td>
                            <a onclick="editPhaseTotalAmount({{$phase->id}}, this)"><i class="ti ti-pencil"></i></a>
                          </td>
                        @endif
                        <td class="fw-bold">Item Total</td>
                        <td class="text-end fw-bold"
                          @if($phase->total_amount_adjustment)
                            data-bs-toggle="tooltip" title="Calculated amount: {{cMoney(($phase->getRawOriginal('total_cost') / 1000), $phase->contract->currency, true)}}"
                          @endif
                            >@cMoney($phase->total_cost - (count($phase->costAdjustments) ? $phase->costAdjustments->sum('amount') : 0), $phase->contract->currency, true)
                              @if($phase->total_amount_adjustment)
                                <span class="text-danger">*</span>
                              @endif
                        </td>
                    </tr>
                    @if($phase->is_allowable_cost && $phase->costAdjustments)
                      @include('admin.pages.contracts.phases.show.cost-adjustments')
                      <tr style="background-color: #efeff1;">
                        @if(!$is_paid)
                          <td>
                            <a onclick="editPhaseTotalAmount({{$phase->id}}, this)"><i class="ti ti-pencil"></i></a>
                          </td>
                        @endif
                        <td class="fw-bold">Grand Total</td>
                        <td class="text-end fw-bold"
                            >@cMoney($phase->total_cost, $phase->contract->currency, true)
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
      @else
        @include('admin.pages.contracts.phases.create-form')
      @endif
    </div>
</div>
