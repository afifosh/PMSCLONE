<div class="content-header mb-3">
  <h6 class="mb-0">Contacts</h6>
  <small>Add Your Contact Persons</small>
</div>
<div class="row mb-3">
  @forelse ($contacts as $contact)
    <div class="col-md-6 mb-md-0 mb-2">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <h6 class="fw-semibold mb-0">John Doe (Default)</h6>
            <span class="badge bg-label-primary">Home</span>
          </span>
          <span class="custom-option-body">
            <small>4135 Parkway Street, Los Angeles, CA, 90017.<br /> Mobile : 1234567890 Cash / Card on delivery available</small>
            <hr class="my-2">
            <span class="d-flex">
              <a class="me-2" href="javascript:void(0)">Edit</a> <a href="javascript:void(0)">Remove</a>
            </span>
          </span>
        </label>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="alert alert-warning">
        <i class="ti ti-alert me-2"></i> No Contact Persons Found.
      </div>
    </div>
  @endforelse
</div>
<div class="col-12 d-flex justify-content-between">
  <button class="btn btn-label-secondary btn-prev" type="button"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
    <span class="align-middle d-sm-inline-block d-none">Previous</span>
  </button>
  <div>
    <button type="button" class="btn btn-label-primary me-2" data-toggle="ajax-modal" data-title="Add New Contact Person" data-href="{{route('company.contacts.create')}}">Add new</button>
    <button class="btn btn-primary btn-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
    <button type="button" data-form="ajax-form" class="d-none"></button>
  </div>
</div>
