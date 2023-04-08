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
          <input id="approval-status-{{$loop->iteration}}" name="approval_status[{{$address['modification_id']}}]" type="hidden" value="1"/>
        {!! Form::hidden('modification_ids[]', $address['modification_id']) !!}
        <div class="col-sm-12 mb-md-3">
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
                <div class="row">
                  @forelse ($fields as $field_title => $field_name)
                      <div class="col-6 my-2">
                          <div class="fw-bold">
                            {{$field_title}}
                          </div>
                          <span class="fst-italic d-flex justify-content-between">
                            <span>{{ substr(is_array(@$address[$field_name])? json_encode($address[$field_name]) : $address[$field_name],0 ,30) }}</span>
                            <div class="me-5">
                              <label class="switch switch-square">
                                <input type="checkbox" class="switch-input" data-switch-toggle-in-all="#rr-{{$loop->parent->iteration}}" data-nset="#approval-status-{{$loop->parent->iteration}}" checked />
                                <span class="switch-toggle-slider">
                                  <span class="switch-on"><i class="ti ti-check"></i></span>
                                  <span class="switch-off"><i class="ti ti-x"></i></span>
                                </span>
                              </label>
                            </div>
                          </span>
                      </div>
                  @empty
                  @endforelse
                  <div class="row d-none mt-2" id="rr-{{$loop->iteration}}">
                    <div class="">
                      <label class="form-label fw-bold">Rejection Reason</label>
                      <textarea class="form-control" name="disapproval_reason[{{$address['modification_id']}}]" rows="3"></textarea>
                    </div>
                  </div>
                </div>
              </span>
            </label>
          </div>
        </div>
      @empty
      @endforelse
    </div>
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <button type="button" class="btn btn-light">Previews</button>
        <button type="submit" class="btn btn-primary">Next</button>
      </div>
    </div>
  </div>
</div>
