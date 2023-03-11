<form action="{{route('company.updateBankAccounts')}}" method="POST">
  @csrf
  <div class="row g-3 form-repeater">
    <div data-repeater-list="bank_accounts">
      @forelse ($bankAccounts as $account)
      <div class="p-3 mt-4 border rounded position-relative" data-repeater-item style="background-color: #f1f0f2;">
        <button class="btn btn-xs rounded-circle  btn-label-danger position-absolute top-0 start-100 translate-middle" type="button" data-repeater-delete>
          <i class="my-2 ti ti-trash ti-xs"></i>
        </button>
        <div class="row">
          {!! Form::hidden('bank_accounts[][id]', @$account['id']) !!}
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Country <span class="text-danger">*</span></label>
            {!! Form::select('bank_accounts[][country_id]',$countries->prepend('Select Country', ''), @$account['country_id'], ['class' => 'form-select select2']) !!}
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Bank Name <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][name]" value="{{@$account['name']}}" class="form-control" placeholder="Bank Name" />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Branch <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][branch]" value="{{@$account['branch']}}" class="form-control" placeholder="Branch" />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Street <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][street]" value="{{@$account['street']}}" class="form-control" placeholder="Street" />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">City <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][city]" value="{{@$account['city']}}" class="form-control" placeholder="City" />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">State <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][state]" value="{{@$account['state']}}" class="form-control" placeholder="State" />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Postal Code <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][post_code]" value="{{@$account['post_code']}}" class="form-control" placeholder="Postal Code" />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Account Number <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][account_no]" value="{{@$account['account_no']}}" class="form-control" placeholder="Account Number" />
          </div>
          <div class="mb-3 col-lg-6 col-xl-6 col-12 mb-0">
            <label class="form-label">IBAN Number <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][iban_no]" value="{{@$account['iban_no']}}" class="form-control" placeholder="IBAN Number" />
          </div>
          <div class="mb-3 col-lg-6 col-xl-6 col-12 mb-0">
            <label class="form-label">Swift Code <span class="text-danger">*</span></label>
            <input type="text" name="bank_accounts[][swift_code]" value="{{@$account['swift_code']}}" class="form-control" placeholder="Swift Code" />
          </div>
        </div>
      </div>
      @empty
      @endforelse
    </div>
    <div class="mb-2 text-end">
      <button class="btn btn-primary" type="button" data-repeater-create>
        <i class="ti ti-plus me-1"></i>
        <span class="align-middle">Add</span>
      </button>
    </div>
  </div>
  <input class="d-none" type="text" name="submit_type">
  <div class="col-12 d-flex justify-content-between">
    <button class="btn btn-label-secondary btn-prev" type="button"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
      <span class="align-middle d-sm-inline-block d-none">Previous</span>
    </button>
    <div>
      @if (!auth()->user()->company->bankAccounts->count())
        <button class="btn btn-outline-secondary save-draft" type="button">Save Draft</button>
      @endif
      <button class="btn btn-primary btn-next" type="button"> <span class="align-middle d-sm-inline-block me-sm-1">Submit</span></button>
      <button type="button" data-form="ajax-form" class="d-none"></button>
    </div>
  </div>
</form>
