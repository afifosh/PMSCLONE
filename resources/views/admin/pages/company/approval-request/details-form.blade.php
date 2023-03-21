<form action="{{route('admin.approval-requests.level.companies.update', ['company' => $company->id, 'level' => $company->approval_level])}}" method="post">
  @csrf
  {!! Form::hidden('modification_ids[]', $detail['modification_id']) !!}
<div class="card-body">
  <div class="d-flex align-items-start align-items-sm-center gap-4">
    {{-- <img src="{{ $detail->avatar }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" /> --}}
  </div>
</div>
<hr>
<div class="row g-3">
  <div class="col-sm-6">
    <label class="form-label">Company Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ $detail['name'] }}" placeholder="Name"  disabled/>
  </div>
  <div class="col-sm-6">
    <label class="form-label">Website <span class="text-danger">*</span></label>
    <input type="text" name="website" class="form-control" value="{{ $detail['website'] }}" placeholder="website" disabled/>
  </div>
  <div class="col-sm-6">
    <label>Locality Type <span class="text-danger">*</span></label>
    {!! Form::select('locality_type', \App\Models\CompanyDetail::LocalityTypes, $detail['locality_type'], ['disabled', 'class' => 'form-control select2']) !!}
  </div>
  <div class="col-sm-6">
    <label class="form-label">Geographical Coverage</label>
    {!! Form::select('geographical_coverage[]', $countries, $detail['geographical_coverage'], ['disabled', 'class' => 'form-controll select2', 'multiple']) !!}
  </div>
  <div class="col-sm-6">
    <label class="form-label">Year Founded <span class="text-danger">*</span></label>
    <input type="date" name="date_founded" value="{{ $detail['date_founded'] }}" class="form-control" disabled/>
  </div>
  <div class="col-sm-6">
    <label class="form-label">D.U.N.S Number</label>
    <input type="text" name="duns_number" value="{{ $detail['duns_number'] }}" class="form-control" placeholder="D.U.N.S Number" disabled/>
  </div>
  <div class="col-sm-6">
    <label for="no-of-employee">Number Of Employees <span class="text-danger">*</span></label>
    {!! Form::select('no_of_employees', \App\Models\CompanyDetail::NoOfEmployee, $detail['no_of_employees'], ['disabled', 'class' => 'form-control select2']) !!}
  </div>
  <div class="col-sm-6">
    <label>Company Legal Form <span class="text-danger">*</span></label>
    {!! Form::select('legal_form', \App\Models\CompanyDetail::LegalForms, $detail['legal_form'], ['disabled', 'class' => 'form-control select2']) !!}
  </div>
  <div class="mb-3 col-12">
    <label for="company_desc" class="form-label">Company Description</label>
    <textarea class="form-control" name="description" id="company_desc" rows="3" disabled> {{ $detail['description'] }}</textarea>
  </div>
  <hr>
  <div class="col-sm-6">
    <label class="form-label">Facebook Link</label>
    <input type="text" name="facebook_url" class="form-control" placeholder="Facebook Link" value="{{ $detail['facebook_url'] }}" disabled/>
  </div>
  <div class="col-sm-6">
    <label class="form-label">Twitter Link</label>
    <input type="text" name="twitter_url" class="form-control" placeholder="Twitter Link" value="{{ $detail['twitter_url'] }}" disabled/>
  </div>
  <div class="col-sm-6">
    <label class="form-label">LinkedIn Link</label>
    <input type="text" name="linkedin_url" class="form-control" placeholder="LinkedIn Link" value="{{ $detail['linkedin_url'] }}" disabled/>
  </div>
  <div class="col-sm-6">
    <label class="form-label">Youtube Link</label>
    <input type="text" name="youtube_url" class="form-control" placeholder="Youtube Link" value="{{ $detail['youtube_url'] }}" disabled/>
  </div>
  <hr>
  <div class="form-check form-switch col-sm-6">
    <input class="form-check-input" name="is_sa_available" {{$detail['sa_company_name'] ? 'checked' : ''}} data-switch-toggle="#sa-c-name" type="checkbox" id="sa-presence" disabled>
    <label class="form-check-label" for="sa-presence">Have you established any presence in Saudi Arabia?</label>
  </div>
  <div class="col-sm-6 mt-0 {{$detail['sa_company_name'] ? '' : 'd-none'}}" id="sa-c-name">
    <label class="form-label">Company name register in Saudi Arabia: <span class="text-danger">*</span></label>
    <input type="text" name="sa_company_name" class="form-control" placeholder="Saudi Arabia Company" value="{{ $detail['sa_company_name'] }}" disabled/>
  </div>
  <hr>
  <div class="form-check form-switch col-sm-6">
    <input class="form-check-input" name="is_subsidory" {{$detail['parent_company'] ? 'checked' : ''}} data-switch-toggle="#is_subsidory" type="checkbox" id="subsidory-confirmation" disabled>
    <label class="form-check-label" for="subsidory-confirmation">Are You a subsidiary Company?</label>
  </div>
  <div class="col-sm-6 mt-0 {{$detail['parent_company'] ? '' : 'd-none'}}" id="is_subsidory">
    <label class="form-label">Please Provide Parent Company Name</label>
    <input type="text" name="parent_company" class="form-control" placeholder="Parent Company" value="{{ $detail['parent_company'] }}" disabled/>
  </div>
  <hr>
  <div class="form-check form-switch col-sm-6">
    <input class="form-check-input" name="is_parent" {{isset($detail['subsidiaries'][0]) ? 'checked' : ''}} data-switch-toggle="#sub-company" type="checkbox" id="pc-confirmation" disabled/>
    <label class="form-check-label" for="pc-confirmation">Are You a Parent Company?</label>
  </div>
  <div class="col-sm-6 mt-0 {{isset($detail['subsidiaries'][0]) ? '' : 'd-none'}}" id="sub-company">
    <label class="form-label">Please Provide Subsidiary Company(s)</label>
    {!! Form::select('subsidiaries[]', isset($detail['subsidiaries'][0]) ? array_combine($detail['subsidiaries'], $detail['subsidiaries']) : [],
      $detail['subsidiaries'], ['disabled', 'class' => 'form-select select2', 'multiple', 'data-tags' => 'true']) !!}
  </div>
  @isset($detail['modification_id'])
    <hr>
    <div class="form-check form-switch col-sm-6 ms-1">
      <label class="form-check-label" for="approval_{{$detail['modification_id']}}">Approval Status</label>
      <input class="form-check-input" id="approval_{{$detail['modification_id']}}" data-switch-toggle-in="#disapproval_block_{{$detail['modification_id']}}" data-inverted name="approval_status[{{$detail['modification_id']}}]" type="checkbox" checked/>
    </div>
    <div class="mb-3 col-12 d-none" id="disapproval_block_{{$detail['modification_id']}}">
      <label for="disapproval_reason" class="form-label">Disapproval Reason <span class="text-danger">*</span></label>
      <textarea class="form-control" name="disapproval_reason[{{$detail['modification_id']}}]" id="disapproval_reason" rows="3"></textarea>
    </div>
  @endisset
  <input class="d-none" type="text" name="submit_type">
  <div class="col-12 d-flex justify-content-between">
    <button class="btn btn-label-secondary btn-prev" disabled> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
      <span class="align-middle d-sm-inline-block d-none">Previous</span>
    </button>
    <div>
      <button class="btn btn-primary btn-next do-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
      <button type="button" data-form="ajax-form" class="d-none"></button>
    </div>
  </div>
</div>
</form>