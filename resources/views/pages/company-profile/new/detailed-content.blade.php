@extends('layouts/layoutMaster')

@section('title', 'User Profile - Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
@endsection

<!-- Page -->
@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection


@section('vendor-script')
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/pages-profile.js')}}"></script>
<script src="{{asset('assets/js/custom/company-profile-page.js')}}"></script>
@endsection

@section('content')
<!-- Header -->
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ asset('assets/img/avatars/14.png') }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>John Doe</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                <li class="list-inline-item">
                  <i class='ti ti-color-swatch'></i> UX Designer
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-map-pin'></i> Vatican City
                </li>
                <li class="list-inline-item">
                  <i class='ti ti-calendar'></i> Joined April 2021</li>
              </ul>
            </div>
            <div>
              <div class="d-flex justify-content-between">
                <span class="">Setup Progress</span>
                <span>{{auth()->user()->company->step_completed_count}}/5</span>
              </div>
              <div class="progress" style="height:10px; width:300px">
                <div class="progress-bar" role="progressbar" style="width: {{(auth()->user()->company->step_completed_count/5)*100}}%" aria-valuenow="{{(auth()->user()->company->step_completed_count/5)*100}}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--/ Header -->

<!-- Navbar pills -->
<div class="row">
  <div class="col-md-12">
    <ul class="nav nav-pills flex-column flex-sm-row mb-4">
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class='ti-xs ti ti-user-check me-1'></i> Profile</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-teams')}}"><i class='ti-xs ti ti-users me-1'></i> Teams</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-projects')}}"><i class='ti-xs ti ti-layout-grid me-1'></i> Projects</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('pages/profile-connections')}}"><i class='ti-xs ti ti-link me-1'></i> Connections</a></li>
    </ul>
  </div>
</div>
<!--/ Navbar pills -->

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-body">
        @include('pages.company-profile.new.header-component', ['head_title' => 'Company Details', 'head_sm' => 'Manage Details'])
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
            <div class="content-end">
              <button class="btn btn-primary submit-and-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Save</span></button>
              <button type="button" data-form="ajax-form" class="d-none"></button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-body">
        @include('pages.company-profile.new.header-component', ['head_title' => 'Contact Persons', 'head_sm' => 'Manage Contact Persons', 'add_new' => route('company.contacts.create'), 'add_title' => 'Add New Contact Person'])
        <hr>
        <div class="row">
          <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
            <div class="form-check custom-option custom-option-basic">
              <label class="form-check-label custom-option-content">
                <span class="custom-option-header mb-2">
                  <span>
                    <h6 class="fw-semibold mb-0">John Doe</h6>
                    <small>CEO</small>
                  </span>
                  <span class="badge bg-label-success">
                    Approved
                  </span>
                </span>
                <span class="custom-option-body">
                  <small>
                    <span class="fw-bold">Type :</span>  Owner <br />
                    <span class="fw-bold">Email :</span>  user1@company1.com <br />
                    <span class="fw-bold">Phone : </span> +96198151849849 <br />
                    <span class="fw-bold">Mobile : </span> +96198151849849 <br />
                    <span class="fw-bold">Fax : </span> +96198151849849 <br />
                    <span class="fw-bold">Is Authorized Person : </span> Yes <br />
                  </small>
                  <hr class="my-2">
                  <span class="d-flex">
                    <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Contact Person" data-href="{{route('company.contacts.show', 1)}}">View</a>
                      <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.contacts.edit', 1)}}">Edit</a>
                      <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.contacts.destroy', 1) }}">Remove</a>
                  </span>
                </span>
              </label>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
            <div class="form-check custom-option custom-option-basic">
              <label class="form-check-label custom-option-content">
                <span class="custom-option-header mb-2">
                  <span>
                    <h6 class="fw-semibold mb-0">John Spencier</h6>
                    <small>CTO</small>
                  </span>
                  <span class="badge bg-label-success">
                    Approved
                  </span>
                </span>
                <span class="custom-option-body">
                  <small>
                    <span class="fw-bold">Type :</span>  Owner <br />
                    <span class="fw-bold">Email :</span>  user1@company1.com <br />
                    <span class="fw-bold">Phone : </span> +96198151849849 <br />
                    <span class="fw-bold">Mobile : </span> +96198151849849 <br />
                    <span class="fw-bold">Fax : </span> +96198151849849 <br />
                    <span class="fw-bold">Is Authorized Person : </span> Yes <br />
                  </small>
                  <hr class="my-2">
                  <span class="d-flex">
                    <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Contact Person" data-href="{{route('company.contacts.show', 1)}}">View</a>
                      <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.contacts.edit', 1)}}">Edit</a>
                      <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.contacts.destroy', 1) }}">Remove</a>
                  </span>
                </span>
              </label>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
            <div class="form-check custom-option custom-option-basic">
              <label class="form-check-label custom-option-content">
                <span class="custom-option-header mb-2">
                  <span>
                    <h6 class="fw-semibold mb-0">Puttin De</h6>
                    <small>Markeing Manager</small>
                  </span>
                  <span class="badge bg-label-success">
                    Approved
                  </span>
                </span>
                <span class="custom-option-body">
                  <small>
                    <span class="fw-bold">Type :</span>  Owner <br />
                    <span class="fw-bold">Email :</span>  user1@company1.com <br />
                    <span class="fw-bold">Phone : </span> +96198151849849 <br />
                    <span class="fw-bold">Mobile : </span> +96198151849849 <br />
                    <span class="fw-bold">Fax : </span> +96198151849849 <br />
                    <span class="fw-bold">Is Authorized Person : </span> Yes <br />
                  </small>
                  <hr class="my-2">
                  <span class="d-flex">
                    <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Contact Person" data-href="{{route('company.contacts.show', 1)}}">View</a>
                      <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.contacts.edit', 1)}}">Edit</a>
                      <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.contacts.destroy', 1) }}">Remove</a>
                  </span>
                </span>
              </label>
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
            <div class="form-check custom-option custom-option-basic">
              <label class="form-check-label custom-option-content">
                <span class="custom-option-header mb-2">
                  <span>
                    <h6 class="fw-semibold mb-0">Puttin De</h6>
                    <small>Markeing Manager</small>
                  </span>
                  <span class="badge bg-label-success">
                    Approved
                  </span>
                </span>
                <span class="custom-option-body">
                  <small>
                    <span class="fw-bold">Type :</span>  Owner <br />
                    <span class="fw-bold">Email :</span>  user1@company1.com <br />
                    <span class="fw-bold">Phone : </span> +96198151849849 <br />
                    <span class="fw-bold">Mobile : </span> +96198151849849 <br />
                    <span class="fw-bold">Fax : </span> +96198151849849 <br />
                    <span class="fw-bold">Is Authorized Person : </span> Yes <br />
                  </small>
                  <hr class="my-2">
                  <span class="d-flex">
                    <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Contact Person" data-href="{{route('company.contacts.show', 1)}}">View</a>
                      <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.contacts.edit', 1)}}">Edit</a>
                      <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.contacts.destroy', 1) }}">Remove</a>
                  </span>
                </span>
              </label>
            </div>
          </div>
          {{-- <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
            <div class="form-check custom-option custom-option-basic h-100">
              <div class="text-center">
                <div class="p-3 border rounded-circle">
                  +
                </div>
                <div >Add New Contact</div>
              </div>
            </div>
          </div> --}}
        </div>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-body">
        @include('pages.company-profile.new.header-component', ['head_title' => 'Company Addresses', 'head_sm' => 'Manage Company Addresses', 'add_new' => route('company.addresses.create'), 'add_title' => 'Add New Address'])
        <hr>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
          <div class="form-check custom-option custom-option-basic">
            <label class="form-check-label custom-option-content">
              <span class="custom-option-header mb-2">
                <span>
                  <h6 class="fw-semibold mb-0">Main Office Address</h6>
                </span>
                <span class="badge bg-label-success">
                  Approved
                </span>
              </span>
              <span class="custom-option-body">
                <small>
                  <span>818 Milton Court </span><br />
                  <span>Id animi perspiciat sdganertn wrntwrt </span><br />
                  <span>Culpa quisquam obcae wbbwre nr</span><br />
                  <span class="fw-bold">Post Code :</span>  98157 <br />
                  <span class="fw-bold">City : </span> Riyadh <br />
                  <span class="fw-bold">State : </span> Riyadh <br />
                  <span class="fw-bold">Country : </span> Saudi Arabia <br />
                </small>
                <hr class="my-2">
                <span class="d-flex">
                  <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Address" data-href="{{route('company.addresses.show', 1)}}">View</a>
                  <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Address" data-href="{{route('company.addresses.edit', 1)}}">Edit</a>
                  <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.addresses.destroy', 1) }}">Remove</a>
                </span>
              </span>
            </label>
          </div>
        </div>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-body">
        @include('pages.company-profile.new.header-component', ['head_title' => 'Verification Documents', 'head_sm' => 'Manage Verification Documents', 'add_new' => route('company.kyc-documents.create'), 'add_title' => 'Add New'])
        <hr>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-body">
        @include('pages.company-profile.new.header-component', ['head_title' => 'Bank Accounts', 'head_sm' => 'Manage Bank Accounts', 'add_new' => route('company.bank-accounts.create'), 'add_title' => 'Add New Account'])
        <hr>
        <div class="col-12">
          <div class="mx-auto text-center">
            <div class="my-5">
              <i class="fa fa-magnifying-glass fa-7x" style="color: #cd545b;"></i>
              <h3>No Account Found!</h3>
              <span>Looks like you have not added any bank account yet. <br> No Worries click the add new button to add a new account</span>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
