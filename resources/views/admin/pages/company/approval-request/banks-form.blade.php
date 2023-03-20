<form action="{{route('admin.approval-requests.level.companies.update', ['company' => $company->id, 'level' => $company->approval_level])}}" method="post">
  @csrf
  <div class="row g-3">
      @forelse ($bankAccounts as $account)
      <div class="p-3 mt-4 border rounded position-relative" style="background-color: #f1f0f2;">
        <div class="row">
          {!! Form::hidden('modification_ids[]', $account['modification_id']) !!}
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Country <span class="text-danger">*</span></label>
            {!! Form::select('bank_accounts[][country_id]',$countries->prepend('Select Country', ''), @$account['country_id'], ['class' => 'form-select select2', 'disabled']) !!}
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Bank Name <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][name]" value="{{@$account['name']}}" class="form-control" placeholder="Bank Name" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Branch <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][branch]" value="{{@$account['branch']}}" class="form-control" placeholder="Branch" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Street <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][street]" value="{{@$account['street']}}" class="form-control" placeholder="Street" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">City <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][city]" value="{{@$account['city']}}" class="form-control" placeholder="City" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">State <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][state]" value="{{@$account['state']}}" class="form-control" placeholder="State" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Postal Code <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][post_code]" value="{{@$account['post_code']}}" class="form-control" placeholder="Postal Code" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Account Number <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][account_no]" value="{{@$account['account_no']}}" class="form-control" placeholder="Account Number" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-6 col-12 mb-0">
            <label class="form-label">IBAN Number <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][iban_no]" value="{{@$account['iban_no']}}" class="form-control" placeholder="IBAN Number" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-6 col-12 mb-0">
            <label class="form-label">Swift Code <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][swift_code]" value="{{@$account['swift_code']}}" class="form-control" placeholder="Swift Code" disabled />
          </div>
        </div>
        @isset($account['modification_id'])
          <hr>
          <div class="form-check form-switch col-sm-6 ms-1">
            <label class="form-check-label" for="approval_{{$account['modification_id']}}">Approval Status</label>
            <input class="form-check-input" id="approval_{{$account['modification_id']}}" data-switch-toggle-in="#disapproval_block_{{$account['modification_id']}}" data-inverted name="approval_status[{{$account['modification_id']}}]" type="checkbox" checked/>
          </div>
          <div class="mb-3 col-12 d-none" id="disapproval_block_{{$account['modification_id']}}">
            <label for="disapproval_reason" class="form-label">Disapproval Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" name="disapproval_reason[{{$account['modification_id']}}]" id="disapproval_reason" rows="3"></textarea>
          </div>
        @endisset
      </div>
      @empty
      @endforelse
  <input class="d-none" type="text" name="submit_type">
  <div class="col-12 d-flex justify-content-between">
    <button class="btn btn-label-secondary btn-prev" type="button"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
      <span class="align-middle d-sm-inline-block d-none">Previous</span>
    </button>
    <div>
      <button class="btn btn-primary btn-next" type="button"> <span class="align-middle d-sm-inline-block me-sm-1">Submit</span></button>
      <button type="button" data-form="ajax-form" class="d-none"></button>
    </div>
  </div>
</form>
