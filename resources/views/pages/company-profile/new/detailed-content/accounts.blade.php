<div class="content-header mb-3 d-sm-flex justify-content-between">
  <div>
    <h6 class="mb-0">Bank Accounts</h6>
    <small>Manage Bank Accounts</small>
  </div>
  <div>
    @if (auth()->user()->company->isEditable())
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="Add New Contact Person" data-href="{{ route('company.bank-accounts.create') }}">Add New Account</button>
      </div>
    @endif
  </div>
</div>
<hr>
<div class="row">
  @forelse ($bankAccounts as $account)
    @php
      $account_original = $account;
      if ($account->modifications->count()) {
        $account = transformModifiedData($account->modifications[0]->modifications) + $account->toArray();
      }
    @endphp
    <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <h6 class="fw-semibold mb-0">{{$account['name']}}</h6>
            <span class="badge bg-label-{{(!$account_original->modifications->count() && $account['id']) ? 'primary' : 'warning'}}">
              {{$account['id'] ? ($account_original->modifications->count() ? 'Partially Approved' : 'Approved') : 'Pending Approval'}}
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
              <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Bank Account" data-href="{{route('company.bank-accounts.show', $account['id'])}}">View</a>
              @if(auth()->user()->company->isEditable())
                <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Bank Account" data-href="{{route('company.bank-accounts.edit', $account['id'])}}">Edit</a>
                <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.bank-accounts.destroy', $account['id']) }}">Remove</a>
              @endif
            </span>
          </span>
        </label>
      </div>
    </div>
  @empty
  @endforelse
  @forelse ($pending_creation_accounts as $pending_account)
  @php
    $account = transformModifiedData($pending_account->modifications);
  @endphp
    <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <h6 class="fw-semibold mb-0">{{$account['name']}}</h6>
            <span class="badge bg-label-{{@$account['id'] ? 'primary' : 'warning'}}">{{@$account['id'] ? 'Approved': ($pending_account->disapprovals()->count() ? 'Rejected': 'Pending Approval')}}</span>
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
              <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Bank Account" data-href="{{route('company.bank-accounts.show', ['bank_account' => $pending_account->id, 'type' => 'pending_creation'])}}">View</a>
              @if(auth()->user()->company->isEditable())
                <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Bank Account" data-href="{{route('company.bank-accounts.edit', ['bank_account' => $pending_account->id, 'type' => 'pending_creation'])}}">Edit</a>
                <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.bank-accounts.destroy', ['bank_account' => $pending_account->id, 'type' => 'pending_creation']) }}">Remove</a>
              @endif
            </span>
          </span>
        </label>
      </div>
    </div>
  @empty
  @endforelse
  @if (!$bankAccounts->count() && !$pending_creation_accounts->count())
  <div class="col-12">
    <div class="mx-auto text-center">
      <div class="my-5">
        <i class="fa fa-magnifying-glass fa-7x" style="color: #cd545b;"></i>
        <h3>No Account Found!</h3>
        <span>Looks like you have not added any bank account yet. <br> No Worries click the add new button to add a new account</span>
      </div>
    </div>
  </div>
  @endif
  <div class="col-12 d-flex justify-content-between">
    <span></span>
    <div>
      @if (auth()->user()->company->isEditable())
        <button type="button" class="btn btn-label-primary me-2" data-toggle="ajax-modal" data-title="Add New Account" data-href="{{route('company.bank-accounts.create')}}">Add new Account</button>
      @endif
      <a href="{{route('company.submitApprovalRequest')}}" class="btn btn-primary {{auth()->user()->company->canBeSentForApproval() ? '': 'disabled'}}" type="button"> <span class="align-middle d-sm-inline-block me-sm-1">Send for Approval</span></a>
    </div>
  </div>
</div>
