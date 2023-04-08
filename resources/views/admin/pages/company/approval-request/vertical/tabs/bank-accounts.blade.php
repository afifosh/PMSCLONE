<div class="card w-100">
  <div class="card-body">
    <h5>Company Bank Accounts</h5>
      <hr>
    <div class="row">
      @forelse ($bankAccounts as $account)
        @php
          $account_original = $account;
          // if ($account->modifications->count()) {
          //   $account = transformModifiedData($account->modifications[0]->modifications) + $account->toArray();
          // }
        @endphp
        <input id="approval-status-{{$loop->iteration}}" name="approval_status[{{$account['modification_id']}}]" type="hidden" value="1"/>
        {!! Form::hidden('modification_ids[]', $account['modification_id']) !!}
        <div class="col-sm-12 mb-md-3">
          <div class="form-check custom-option custom-option-basic">
            <label class="form-check-label custom-option-content">
              <span class="custom-option-header mb-2">
                <h6 class="fw-semibold mb-0">{{$account['name']}}</h6>
                <span class="badge bg-label-{{(@$account['id']) ? 'success' : 'warning'}}">
                  {{@$account['id'] ? 'Partially Approved' : 'Pending Approval'}}
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
                            <span>{{ substr(is_array(@$account[$field_name])? json_encode($account[$field_name]) : $account[$field_name],0 ,30) }}</span>
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
                      <textarea class="form-control" name="disapproval_reason[{{$account['modification_id']}}]" rows="3"></textarea>
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
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>
