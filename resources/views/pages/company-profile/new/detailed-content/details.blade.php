<div class="card-body pt-0">
  <form action="{{route('company.updateDetails')}}" method="post">
    @csrf
    <div class="card-body">
      <div class="d-flex align-items-start align-items-sm-center gap-4">
        <img src="{{ auth()->user()->company->getPOCLogoUrl() }}" data-default="{{ auth()->user()->company->getPOCLogoUrl() }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
        <div class="button-wrapper">
          <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
            <span class="d-none d-sm-block">Upload Logo</span>
            <i class="ti ti-upload d-block d-sm-none"></i>
          </label>
          <button type="button" class="btn btn-label-secondary account-image-reset mb-3">
            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
            <span class="d-none d-sm-block">Reset</span>
          </button>
          <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 800K</div>
          <input name="logo" type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
        </div>
      </div>
    </div>
    <hr>
    <div class="row g-3">
      <div class="col-sm-6">
        <label class="form-label">Company Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ @$detail['name'] }}" placeholder="Name" />
        @modificationAlert(@$modifications['name'])
      </div>
      <div class="col-sm-6">
        <label class="form-label">Website <span class="text-danger">*</span></label>
        <input type="text" name="website" class="form-control" value="{{ @$detail['website'] }}" placeholder="website"/>
        @modificationAlert(@$modifications['website'])
      </div>
      <div class="col-sm-6">
        <label>Locality Type <span class="text-danger">*</span></label>
        {!! Form::select('locality_type', \App\Models\CompanyDetail::LocalityTypes, @$detail['locality_type'], ['class' => 'form-control select2']) !!}
      </div>
      <div class="col-sm-6">
        <label class="form-label">Geographical Coverage</label>
        {!! Form::select('geographical_coverage[]', $countries, @$detail['geographical_coverage'], ['class' => 'form-controll select2', 'multiple']) !!}
      </div>
      <div class="col-sm-6">
        <label class="form-label">Year Founded <span class="text-danger">*</span></label>
        <input type="date" name="date_founded" value="{{ @$detail['date_founded'] }}" class="form-control"/>
        @modificationAlert(@$modifications['date_founded'])
      </div>
      <div class="col-sm-6">
        <label class="form-label">D.U.N.S Number</label>
        <input type="text" name="duns_number" value="{{ @$detail['duns_number'] }}" class="form-control" placeholder="D.U.N.S Number"/>
        @modificationAlert(@$modifications['duns_number'])
      </div>
      <div class="col-sm-6">
        <label for="no-of-employee">Number Of Employees <span class="text-danger">*</span></label>
        {!! Form::select('no_of_employees', \App\Models\CompanyDetail::NoOfEmployee, @$detail['no_of_employees'], ['class' => 'form-control select2']) !!}
      </div>
      <div class="col-sm-6">
        <label>Company Legal Form <span class="text-danger">*</span></label>
        {!! Form::select('legal_form', \App\Models\CompanyDetail::LegalForms, @$detail['legal_form'], ['class' => 'form-control select2']) !!}
      </div>
      <div class="mb-3 col-12">
        <label for="company_desc" class="form-label">Company Description</label>
        <textarea class="form-control" name="description" id="company_desc" rows="3"> {{ @$detail['description'] }}</textarea>
      </div>
      <hr>
      <div class="col-sm-6">
        <label class="form-label">Facebook Link</label>
        <input type="text" name="facebook_url" class="form-control" placeholder="Facebook Link" value="{{ @$detail['facebook_url'] }}"/>
        @modificationAlert(@$modifications['facebook_url'])
      </div>
      <div class="col-sm-6">
        <label class="form-label">Twitter Link</label>
        <input type="text" name="twitter_url" class="form-control" placeholder="Twitter Link" value="{{ @$detail['twitter_url'] }}"/>
        @modificationAlert(@$modifications['twitter_url'])
      </div>
      <div class="col-sm-6">
        <label class="form-label">LinkedIn Link</label>
        <input type="text" name="linkedin_url" class="form-control" placeholder="LinkedIn Link" value="{{ @$detail['linkedin_url'] }}"/>
        @modificationAlert(@$modifications['linkedin_url'])
      </div>
      <div class="col-sm-6">
        <label class="form-label">Youtube Link</label>
        <input type="text" name="youtube_url" class="form-control" placeholder="Youtube Link" value="{{ @$detail['youtube_url'] }}"/>
        @modificationAlert(@$modifications['youtube_url'])
      </div>
      <hr>
      <div class="form-check form-switch col-sm-6">
        <input class="form-check-input" name="is_sa_available" {{@$detail['sa_company_name'] ? 'checked' : ''}} data-switch-toggle="#sa-c-name" type="checkbox" id="sa-presence">
        <label class="form-check-label" for="sa-presence">Have you established any presence in Saudi Arabia?</label>
      </div>
      <div class="col-sm-6 mt-0 {{@$detail['sa_company_name'] ? '' : 'd-none'}}" id="sa-c-name">
        <label class="form-label">Company name register in Saudi Arabia: <span class="text-danger">*</span></label>
        <input type="text" name="sa_company_name" class="form-control" placeholder="Saudi Arabia Company" value="{{ @$detail['sa_company_name'] }}"/>
        @modificationAlert(@$modifications['sa_company_name'])
      </div>
      <hr>
      <div class="form-check form-switch col-sm-6">
        <input class="form-check-input" name="is_subsidory" {{@$detail['parent_company'] ? 'checked' : ''}} data-switch-toggle="#is_subsidory" type="checkbox" id="subsidory-confirmation">
        <label class="form-check-label" for="subsidory-confirmation">Are You a subsidiary Company?</label>
      </div>
      <div class="col-sm-6 mt-0 {{@$detail['parent_company'] ? '' : 'd-none'}}" id="is_subsidory">
        <label class="form-label">Please Provide Parent Company Name</label>
        <input type="text" name="parent_company" class="form-control" placeholder="Parent Company" value="{{ @$detail['parent_company'] }}"/>
        @modificationAlert(@$modifications['parent_company'])
      </div>
      <hr>
      <div class="form-check form-switch col-sm-6">
        <input class="form-check-input" name="is_parent" {{@$detail['subsidiaries'][0] ? 'checked' : ''}} data-switch-toggle="#sub-company" type="checkbox" id="pc-confirmation">
        <label class="form-check-label" for="pc-confirmation">Are You a Parent Company?</label>
      </div>
      <div class="col-sm-6 mt-0 {{ @$detail['subsidiaries'][0] ? '' : 'd-none'}}" id="sub-company">
        <label class="form-label">Please Provide Subsidiary Company(s)</label>
        {!! Form::select('subsidiaries[]', @$detail['subsidiaries'][0] ? array_combine(@$detail['subsidiaries'], @$detail['subsidiaries']) : [],
          @$detail['subsidiaries'], ['class' => 'form-select select2', 'multiple', 'data-tags' => 'true']) !!}
      </div>
      <input class="d-none" type="text" name="submit_type">
      <div class="d-flex justify-content-between">
        <span></span>
        <div>
          <button class="btn btn-primary submit-and-next {{auth()->user()->company->isEditable() ? '' : 'disabled'}}" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Update</span></button>
          <button type="button" data-form="ajax-form" class="d-none"></button>
        </div>
      </div>
    </div>
  </form>
</div>
