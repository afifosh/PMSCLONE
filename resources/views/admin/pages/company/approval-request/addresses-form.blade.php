<div class="row g-3 form-repeater">
    @forelse ($addresses as $address)
      <div class="p-3 mt-4 border rounded position-relative" style="background-color: #f1f0f2;">
        <div class="row">
          {!! Form::hidden('modification_ids[]', $address['modification_id']) !!}
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label" for="form-repeater-1-1">Address Name</label>
            <input type="text" class="form-control" name="addresses[][name]" value="{{@$address['name']}}" placeholder="Address Name" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Country</label>
            {!! Form::select('country_id', $countries->prepend('Select Country', ''), @$address['country_id'], ['class' => 'form-controll select2', 'disabled']) !!}
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Address Line 1</label>
            <input type="text" class="form-control" name="addresses[][address_line_1]" value="{{@$address['address_line_1']}}" placeholder="Address Line 1" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Address Line 2</label>
            <input type="text" class="form-control" name="addresses[][address_line_2]" value="{{@$address['address_line_2']}}" placeholder="Address Line 2" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Address Line 3</label>
            <input type="text" class="form-control" name="addresses[][address_line_3]" value="{{@$address['address_line_3']}}" placeholder="Address Line 3" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Website</label>
            <input type="text" class="form-control" name="addresses[][website]" value="{{@$address['website']}}" placeholder="Website" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">City/Town/Locality</label>
            <input type="text" class="form-control" name="addresses[][city]" value="{{@$address['city']}}" placeholder="City/Town/Locality" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">State</label>
            <input type="text" class="form-control" name="addresses[][state]" value="{{@$address['state']}}" placeholder="State" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Province</label>
            <input type="text" class="form-control" name="addresses[][province]" value="{{@$address['province']}}" placeholder="Province" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label" >Postal Code</label>
            <input type="text" class="form-control" name="addresses[][postal_code]" value="{{@$address['postal_code']}}" placeholder="Postal Code" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label" >Phone</label>
            <input type="text" class="form-control" name="addresses[][phone]" value="{{@$address['phone']}}" placeholder="Phone" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label" >Fax</label>
            <input type="text" class="form-control" name="addresses[][fax]" value="{{@$address['fax']}}" placeholder="Fax" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label" >Email</label>
            <input type="email" name="addresses[][email]" value="{{@$address['email']}}" class="form-control" placeholder="Email" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label">Zip Code</label>
            <input type="text" class="form-control" name="addresses[][zip]" value="{{@$address['zip']}}" placeholder="Zip Code" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label" >Latitude</label>
            <input type="text" class="form-control" name="addresses[][latitude]" value="{{@$address['latitude']}}" placeholder="Latitude" disabled />
          </div>
          <div class="mb-3 col-lg-6 col-xl-3 col-12 mb-0">
            <label class="form-label" >Longitude</label>
            <input type="text" class="form-control" name="addresses[][longitude]" value="{{@$address['longitude']}}" placeholder="Longitude" disabled />
          </div>
          <div class="d-flex justify-content-end">
            <div class="ps-2">
              <input class="form-check-input" name="addresses[][address_type]" @checked(in_array('purchasing', $address['address_type'] ?? [])) value="purchasing" type="checkbox" disabled>
              <label class="form-check-label">
                Purchasing Address
              </label>
            </div>
            <div class="ps-2">
              <input class="form-check-input" name="addresses[][address_type]" @checked(in_array('billing', $address['address_type'] ?? [])) value="billing" type="checkbox" disabled>
              <label class="form-check-label">
                Payment Address
              </label>
            </div>
            <div class="ps-2">
              <input class="form-check-input" name="addresses[][address_type]" @checked(in_array('rfp_only', $address['address_type'] ?? [])) value="rfp_only" type="checkbox" disabled>
              <label class="form-check-label">
                RFP Only Address
              </label>
            </div>
          </div>
        </div>
        @isset($address['modification_id'])
          <hr>
          <div class="form-check form-switch col-sm-6 ms-1">
            <label class="form-check-label" for="approval_{{$address['modification_id']}}">Approval Status</label>
            <input class="form-check-input" id="approval_{{$address['modification_id']}}" data-switch-toggle-in="#disapproval_block_{{$address['modification_id']}}" data-inverted name="approval_status[{{$address['modification_id']}}]" type="checkbox" checked/>
          </div>
          <div class="mb-3 col-12 d-none" id="disapproval_block_{{$address['modification_id']}}">
            <label for="disapproval_reason" class="form-label">Disapproval Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" name="disapproval_reason[{{$address['modification_id']}}]" id="disapproval_reason" rows="3"></textarea>
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
      <button class="btn btn-primary" onclick="triggerNext();" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
    </div>
  </div>
</div>
