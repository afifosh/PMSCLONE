<div class="card w-100">
  <div class="card-body">
    <h5>Company Bank Accounts</h5>
      <hr>
    <div class="row">
      @forelse ($approved_bank_accounts as $account)
      @php
        $account_original = $account;
        $modifications = [];
        $isEditable = false;
        $status = 'approved';
        $approvals = @$account_original->modifications[0]->approvals ?? [];
        $disapprovals = @$account_original->modifications[0]->disapprovals ?? [];
        if ($account->modifications->count()) {
          if($account_original->modifications[0]->approvals->count() < $level && !$account_original->modifications[0]->disapprovals->count()){
            $isEditable = true;
            $status = 'partially approved';
            $modifications = transformModifiedData($account_original->modifications[0]->modifications);
            $account['modification_id'] = $account_original->modifications[0]->id;
          }
          $account = transformModifiedData($account_original->modifications[0]->modifications) + $account->toArray();
        }
        if ($account_original->modifications->count() && $account_original->modifications[0]->disapprovals->count()) {
          $status = 'rejected';
        }
      @endphp
      @include('admin.pages.company.approval-request.vertical.tabs.components.bank-account', ['account' => $account])
      @php
          unset($modifications, $account, $account_original);
      @endphp
      @empty
      @endforelse

      @forelse ($bankAccounts as $account)
        @php
          $account_original = $account;
          $isEditable = true;
          $status = 'pending';
          $approvals = $account_original->approvals;
          $disapprovals = $account_original->disapprovals;
          if(isset($account) && ($account->approvals_count >= $level || $account->disapprovals_count)) {
              $isEditable = false;
              if($account->approvals_count >= $level){
                $status = 'approved';
              }elseif ($account->disapprovals_count) {
                $status = 'rejected';
              }
          }
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
