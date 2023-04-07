<div class="card w-100">
  <div class="card-body">
    <h5>Company Bank Accounts</h5>
      <hr>
    <div class="row">
      @forelse ($bankAccounts as $account)
        @php
          $account_original = $account;
          // if ($account->modifications->count()) {
          //   $account = transformModifiedData($account->modifications[0]->modifications) + $account->toArray();
          // }
        @endphp
        <div class="col-md-4 col-sm-6 mb-md-3">
          <div class="form-check custom-option custom-option-basic">
            <label class="form-check-label custom-option-content">
              <span class="custom-option-header mb-2">
                <h6 class="fw-semibold mb-0">{{$account['name']}}</h6>
                <span class="badge bg-label-{{(@$account['id']) ? 'success' : 'warning'}}">
                  {{@$account['id'] ? 'Partially Approved' : 'Pending Approval'}}
                </span>
              </span>
              <span class="custom-option-body">
                <small>
                  <span class="fw-bold">Account Number :</span>  {{ $account['account_no'] }} <br />
                  <span class="fw-bold">IBAN Number :</span>  {{ $account['iban_no'] }} <br />
                  <span class="fw-bold">Swift Code : </span> {{ $account['swift_code'] }} <br />
                  <span class="fw-bold">Branch : </span> {{ $account['branch'] }} <br />
                  <span class="fw-bold">Postal Code : </span> {{ $account['post_code'] }} <br />
                  <span class="fw-bold">City : </span> {{ $account['city'] }} <br />
                  <span class="fw-bold">State : </span> {{ $account['state'] }} <br />
                  <span class="fw-bold">Country : </span> {{ $countries[$account['country_id']] }} <br />
                </small>
                <hr class="my-2">
                <span class="d-flex">
                  <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Bank Account" data-href="{{route('company.bank-accounts.show', 1)}}">View</a>
                </span>
              </span>
            </label>
          </div>
        </div>
      @empty
      @endforelse
    </div>
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <button type="submit" class="btn btn-light">Previews</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>
