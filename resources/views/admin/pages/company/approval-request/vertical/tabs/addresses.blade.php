<div class="card w-100">
  <div class="card-body">
    <h5>Company Addresses</h5>
      <hr>
    <div class="row">
      @forelse ($addresses as $address)
        @php
          $address_original = $address;
          // if ($address->modifications->count()) {
          //   $address = transformModifiedData($address->modifications[0]->modifications) + $address->toArray();
          // }
        @endphp
        <div class="col-md-4 col-sm-6 mb-md-3">
          <div class="form-check custom-option custom-option-basic">
            <label class="form-check-label custom-option-content">
              <span class="custom-option-header mb-2">
                <span>
                  <h6 class="fw-semibold mb-0">{{ @$address['name'] }}</h6>
                </span>
                <span class="badge bg-label-{{(@$address['id']) ? 'success' : 'warning'}}">
                  {{@$address['id'] ? (@$address_original->modifications->count() ? 'Partially Approved' : 'Approved') : 'Pending Approval'}}
                </span>
              </span>
              <span class="custom-option-body">
                <small>
                  <span> {{ @$address['address_line_1'] }} </span><br />
                  <span> {{ @$address['address_line_2'] }} </span><br />
                  <span> {{ @$address['address_line_3'] }}</span><br />
                  <span class="fw-bold">Post Code :</span>  {{ @$address['postal_code'] }} <br />
                  <span class="fw-bold">City : </span> {{ @$address['city'] }} <br />
                  <span class="fw-bold">State : </span> {{ @$address['state'] }} <br />
                  <span class="fw-bold">Country : </span> {{ $countries[@$address['country_id']] }} <br />
                </small>
                <hr class="my-2">
                <span class="d-flex">
                  <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Address" data-href="{{route('company.addresses.show', 1)}}">View</a>
                </span>
              </span>
            </label>
          </div>
        </div>
      @empty
      @endforelse
    </div>
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <button type="submit" class="btn btn-light">Previews</button>
        <button type="submit" class="btn btn-primary">Next</button>
      </div>
    </div>
  </div>
</div>
