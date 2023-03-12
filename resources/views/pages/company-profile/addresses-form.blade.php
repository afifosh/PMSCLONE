<form action="{{route('company.updateAddresses')}}" method="POST">
  <div class="row g-3 form-repeater">
    <div data-repeater-list="addresses">
      @forelse ($addresses as $address)
        <div class="p-3 mt-4 border rounded position-relative" data-repeater-item style="background-color: #f1f0f2;">
            <button class="btn btn-xs rounded-circle  btn-label-danger position-absolute top-0 start-100 translate-middle" type="button" data-repeater-delete>
              <i class="my-2 ti ti-trash ti-xs"></i>
            </button>
          {!! Form::hidden('addresses[][id]', @$address['id']) !!}
          <div class="row">
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label" for="form-repeater-1-1">Address Name</label>
              <input type="text" class="form-control" name="addresses[][name]" value="{{@$address['name']}}" placeholder="Address Name" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">Country</label>
              {!! Form::select('country_id', $countries->prepend('Select Country', ''), @$address['country_id'], ['class' => 'form-controll select2']) !!}
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">Address Line 1</label>
              <input type="text" class="form-control" name="addresses[][address_line_1]" value="{{@$address['address_line_1']}}" placeholder="Address Line 1" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">Address Line 2</label>
              <input type="text" class="form-control" name="addresses[][address_line_2]" value="{{@$address['address_line_2']}}" placeholder="Address Line 2" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">Address Line 3</label>
              <input type="text" class="form-control" name="addresses[][address_line_3]" value="{{@$address['address_line_3']}}" placeholder="Address Line 3" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">Website</label>
              <input type="text" class="form-control" name="addresses[][website]" value="{{@$address['website']}}" placeholder="Website" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">City/Town/Locality</label>
              <input type="text" class="form-control" name="addresses[][city]" value="{{@$address['city']}}" placeholder="City/Town/Locality" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">State</label>
              <input type="text" class="form-control" name="addresses[][state]" value="{{@$address['state']}}" placeholder="State" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">Province</label>
              <input type="text" class="form-control" name="addresses[][province]" value="{{@$address['province']}}" placeholder="Province" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label" >Postal Code</label>
              <input type="text" class="form-control" name="addresses[][postal_code]" value="{{@$address['postal_code']}}" placeholder="Postal Code" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label" >Phone</label>
              <input type="text" class="form-control" name="addresses[][phone]" value="{{@$address['phone']}}" placeholder="Phone" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label" >Fax</label>
              <input type="text" class="form-control" name="addresses[][fax]" value="{{@$address['fax']}}" placeholder="Fax" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label" >Email</label>
              <input type="email" name="addresses[][email]" value="{{@$address['email']}}" class="form-control" placeholder="Email" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label">Zip Code</label>
              <input type="text" class="form-control" name="addresses[][zip]" value="{{@$address['zip']}}" placeholder="Zip Code" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label" >Latitude</label>
              <input type="text" class="form-control" name="addresses[][latitude]" value="{{@$address['latitude']}}" placeholder="Latitude" />
            </div>
            <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
              <label class="form-label" >Longitude</label>
              <input type="text" class="form-control" name="addresses[][longitude]" value="{{@$address['longitude']}}" placeholder="Longitude" />
            </div>
            <div class="d-flex justify-content-end">
              <div class="ps-2">
                <input class="form-check-input" name="addresses[][address_type]" @checked(in_array('purchasing', $address['address_type'] ?? [])) value="purchasing" type="checkbox">
                <label class="form-check-label">
                  Purchasing Address
                </label>
              </div>
              <div class="ps-2">
                <input class="form-check-input" name="addresses[][address_type]" @checked(in_array('billing', $address['address_type'] ?? [])) value="billing" type="checkbox">
                <label class="form-check-label">
                  Payment Address
                </label>
              </div>
              <div class="ps-2">
                <input class="form-check-input" name="addresses[][address_type]" @checked(in_array('rfp_only', $address['address_type'] ?? [])) value="rfp_only" type="checkbox">
                <label class="form-check-label">
                  RFP Only Address
                </label>
              </div>
            </div>
          </div>
        </div>
      @empty
      @endforelse
    </div>
    <div class="mb-0 text-end">
      <button class="btn btn-primary" type="button" data-repeater-create>
        <i class="ti ti-plus me-1"></i>
        <span class="align-middle">Add</span>
      </button>
    </div>
    <input class="d-none" type="text" name="submit_type">
    <div class="col-12 d-flex justify-content-between">
      <button class="btn btn-label-secondary btn-prev" type="button"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
        <span class="align-middle d-sm-inline-block d-none">Previous</span>
      </button>
      <div>
        @if (!auth()->user()->company->addresses->count())
          <button class="btn btn-outline-secondary save-draft" type="button">Save Draft</button>
        @endif
        <button class="btn btn-primary btn-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
        <button type="button" data-form="ajax-form" class="d-none"></button>
      </div>
    </div>
  </div>
</form>
