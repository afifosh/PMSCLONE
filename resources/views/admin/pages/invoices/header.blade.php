
<div class="mt-3  col-12">
  <div class="card h-100">
    <div class="card-header">
      <div class="d-flex justify-content-between mb-3">
        <h5 class="card-title mb-0">{{__('Invoices Summary')}}</h5>
      </div>
    </div>
    <div class="card-body">
      <div class="row gy-3 d-md-flex justify-content-between">
        <div class="col-md-2 col-6">
          <div class="d-flex align-items-center">
            <div class="badge rounded-pill bg-label-info me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
            <div class="card-info">
              <h5 class="mb-0">@cMoney($summary['total'] ?? 0, null, true)</h5>
              <small>{{__('Total Amount')}}</small>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-6">
          <div class="d-flex align-items-center">
            <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
            <div class="card-info">
              <h5 class="mb-0">@cMoney($summary['paid_amount'] ?? 0, null, true)</h5>
              <small>{{__('Paid Amount')}}</small>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-6">
          <div class="d-flex align-items-center">
            <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
            <div class="card-info">
              <h5 class="mb-0">@cMoney($summary['due_amount'] ?? 0, null, true)</h5>
              <small>{{__('Due Amount')}}</small>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-6">
          <div class="d-flex align-items-center">
            <div class="badge rounded-pill bg-label-danger me-3 p-2"><i class="ti ti-shopping-cart ti-sm"></i></div>
            <div class="card-info">
              <h5 class="mb-0">{{$overdue}}</h5>
              <small>{{__('Over Due Invoices')}}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
