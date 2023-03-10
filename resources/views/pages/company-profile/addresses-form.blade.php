<div class="row g-3 form-repeater">
  <div data-repeater-list="group-a">
    <div class="p-3 mt-4 border rounded position-relative" data-repeater-item style="background-color: #f1f0f2;">
        <button class="btn btn-xs rounded-circle  btn-label-danger position-absolute top-0 start-100 translate-middle" data-repeater-delete>
          <i class="ti ti-x ti-xs"></i>
        </button>
      <div class="row">
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label" for="form-repeater-1-1">Address Name</label>
          <input type="text" id="form-repeater-1-1" class="form-control" placeholder="Address Name" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Country</label>
          <select class="form-select">
            <option value="1">Owner</option>
            <option value="2">Employee</option>
          </select>
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Address Line 1</label>
          <input type="text" class="form-control" placeholder="Address Line 1" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Address Line 2</label>
          <input type="text" class="form-control" placeholder="Address Line 2" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Address Line 3</label>
          <input type="text" class="form-control" placeholder="Address Line 3" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Website</label>
          <input type="text" class="form-control" placeholder="Website" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">City/Town/Locality</label>
          <input type="text" class="form-control" placeholder="City/Town/Locality" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">State</label>
          <input type="text" class="form-control" placeholder="State" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Province</label>
          <input type="text" class="form-control" placeholder="Province" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label" >Postal Code</label>
          <input type="text" class="form-control" placeholder="Postal Code" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label" >Phone</label>
          <input type="text" class="form-control" placeholder="Phone" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label" >Fax</label>
          <input type="text" class="form-control" placeholder="Fax" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label" >Email</label>
          <input type="email" class="form-control" placeholder="Email" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label">Zip Code</label>
          <input type="text" class="form-control" placeholder="Zip Code" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label" >Latitude</label>
          <input type="text" class="form-control" placeholder="Latitude" />
        </div>
        <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
          <label class="form-label" >Longitude</label>
          <input type="text" class="form-control" placeholder="Longitude" />
        </div>
        <div class="d-flex justify-content-end">
          <div class="ps-2">
            <input class="form-check-input" type="checkbox" value="1" id="">
            <label class="form-check-label" for="">
              Purchasing Address
            </label>
          </div>
          <div class="ps-2">
            <input class="form-check-input" type="checkbox" value="1" id="" checked>
            <label class="form-check-label" for="">
              Payment Address
            </label>
          </div>
          <div class="ps-2">
            <input class="form-check-input" type="checkbox" value="1" id="" checked>
            <label class="form-check-label" for="">
              RFP Only Address
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="mb-0 text-end">
    <button class="btn btn-primary" data-repeater-create>
      <i class="ti ti-plus me-1"></i>
      <span class="align-middle">Add</span>
    </button>
  </div>
  <div class="col-12 d-flex justify-content-between">
    <button class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
      <span class="align-middle d-sm-inline-block d-none">Previous</span>
    </button>
    <div>
      <button class="btn btn-outline-secondary" type="button">Save Draft</button>
      <button class="btn btn-primary btn-next"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
    </div>
  </div>
</div>
