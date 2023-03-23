@include('pages.company-profile.header-component', ['head_title' => 'Bank Accounts', 'head_sm' => 'Manage Accounts'])
<div class="row mb-3">
  @forelse ($bankAccounts as $account)
    <div class="col-md-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <h6 class="fw-semibold mb-0">{{ $account['account_no']}} ({{$account['name']}})</h6>
            <span class="badge bg-label-primary">{{$account['id'] ? 'Approved': 'Pending Approval'}}</span>
          </span>
          <span class="custom-option-body">
            <small>IBAN Number : {{$account['iban_no']}}<br /> Swift Code : {{$account['swift_code']}}</small>
            <hr class="my-2">
            <span class="d-flex">
              <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.bank-accounts.edit', $account['id'])}}">Edit</a>
              <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.bank-accounts.destroy', $account['id']) }}">Remove</a>
            </span>
          </span>
        </label>
      </div>
    </div>
  @empty
  <div class="col-12">
    <div class="mx-auto text-center">
      <div class="my-5">
        <i class="fa fa-magnifying-glass fa-7x" style="color: #cd545b;"></i>
        <h3>No Account Found!</h3>
        <span>Looks like you have not added any bank account yet. <br> No Worries click the add new button to add a new account</span>
      </div>
    </div>
  </div>
  @endforelse
</div>
<div class="col-12 d-flex justify-content-between">
  <button class="btn btn-label-secondary btn-prev" type="button"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
    <span class="align-middle d-sm-inline-block d-none">Previous</span>
  </button>
  <div>
    <button type="button" class="btn btn-label-primary me-2" data-toggle="ajax-modal" data-title="Add New Account" data-href="{{route('company.bank-accounts.create')}}">Add new Account</button>
    <a href="{{route('company.submitApprovalRequest')}}" class="btn btn-primary {{auth()->user()->company->canBeSentForApproval() ? '': 'disabled'}}" type="button"> <span class="align-middle d-sm-inline-block me-sm-1">Send for Approval</span></a>
  </div>
</div>
