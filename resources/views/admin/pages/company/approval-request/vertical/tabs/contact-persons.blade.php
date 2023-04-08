<div class="card w-100">
  <div class="card-body">
    <h5>Contact Persons</h5>
      <hr>
      <div class="row">
        @forelse ($contacts as $contact)
          @php
            $contact_original = $contact;
            // if ($contact->modifications->count()) {
            //   $contact = transformModifiedData($contact->modifications[0]->modifications) + $contact->toArray();
            // }
          @endphp
          <input id="approval-status-{{$loop->iteration}}" name="approval_status[{{$contact['modification_id']}}]" type="hidden" value="1"/>
          {!! Form::hidden('modification_ids[]', $contact['modification_id']) !!}
          <div class="col-sm-12 mb-md-3">
            <div class="form-check custom-option custom-option-basic">
              <label class="form-check-label custom-option-content">
                <span class="custom-option-header mb-2">
                  <span>
                    <h6 class="fw-semibold mb-0">{{@$contact['first_name']}} {{@$contact['last_name']}}</h6>
                    <small>{{@$contact['position']}}</small>
                  </span>
                  <span class="badge bg-label-{{(@$contact['id']) ? 'success' : 'warning'}}">
                    {{@$contact['id'] ? (@$contact_original->modifications->count() ? 'Partially Approved' : 'Approved') : 'Pending Approval'}}
                  </span>
                </span>
                <span class="custom-option-body">
                  <div class="row">
                    @forelse ($fields as $field_title => $field_name)
                    @if ($field_name == 'poa')
                      @continue
                    @endif
                        <div class="col-6 my-2">
                            <div class="fw-bold">
                              {{$field_title}}
                            </div>
                            <span class="fst-italic d-flex justify-content-between">
                              <span>{{ is_array(@$contact[$field_name])? json_encode($contact[$field_name]) : $contact[$field_name] }}</span>
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
                        <textarea class="form-control" name="disapproval_reason[{{$contact['modification_id']}}]" rows="3"></textarea>
                      </div>
                    </div>
                  </div>
                  {{-- <hr class="my-2">
                  <span class="d-flex">
                    <a class="me-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#approval-modal">View</a>
                  </span>
                </span> --}}
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
</div>
{{-- <div class="modal fade" id="approval-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @include('admin.pages.company.approval-request.vertical.tabs.details', ['body_col' => 12])
      </div>
    </div>
  </div>
</div> --}}
<!--/ Add New Credit Card Modal -->

