<div class="row g-3 form-repeater">
  <div data-repeater-list="group-a">
    <div class="p-3 mt-4 border rounded position-relative" data-repeater-item style="background-color: #f1f0f2;">
        <button class="btn btn-xs rounded-circle  btn-label-danger position-absolute top-0 start-100 translate-middle" data-repeater-delete>
          <i class="ti ti-x ti-xs"></i>
        </button>
      <div class="row">
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Country <span class="text-danger">*</span></label>
          <select class="form-select">
            <option value="1">Owner</option>
            <option value="2">Employee</option>
          </select>
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Bank Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="Bank Name" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Branch <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="Branch" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Street <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="Street" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">City <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="City" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">State <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="State" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Postal Code <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="Postal Code" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Account Number <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="Account Number" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-6 col-12 mb-0">
          <label class="form-label">IBAN Number <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="IBAN Number" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-6 col-12 mb-0">
          <label class="form-label">Swift Code <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="Swift Code" />
        </div>
      </div>
    </div>
  </div>
  <div class="mb-2 text-end">
    <button class="btn btn-primary" data-repeater-create>
      <i class="ti ti-plus me-1"></i>
      <span class="align-middle">Add</span>
    </button>
  </div>
</div>
<div class="col-12 d-flex justify-content-between">
  <button class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
    <span class="align-middle d-sm-inline-block d-none">Previous</span>
  </button>
  <div>
    <button class="btn btn-outline-secondary" type="button">Save Draft</button>
    <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Submit</span> <i class="ti ti-arrow-right"></i></button>
  </div>
</div>
