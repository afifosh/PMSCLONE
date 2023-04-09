<div class="card w-100">
  <div class="card-body">
    <h5>Company Bank Accounts</h5>
      <hr>
    <div class="row">
      @forelse ($approved_bank_accounts as $account)
      @include('admin.pages.company.approval-request.vertical.tabs.components.bank-account', ['account' => $account])
      @empty
      @endforelse

      @forelse ($bankAccounts as $account)
        @php
          $account_original = $account;
          $account = transformModifiedData($account->modifications);
          $account['modification_id'] = $account_original->id;
        @endphp
        @include('admin.pages.company.approval-request.vertical.tabs.components.bank-account', ['account' => $account])
      @empty
      @endforelse
    </div>
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'documents']) }}" class="btn btn-light">Previews</a>
        <button type="button" class="btn btn-primary disabled">Next</button>
      </div>
    </div>
  </div>
</div>
