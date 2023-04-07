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
          <div class="col-md-4 col-sm-6 mb-md-3">
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
                  <small>
                    <span class="fw-bold">Type :</span>  {{@$contact['type'] == 1 ? 'Owner' : 'Employee'}} <br />
                    <span class="fw-bold">Email :</span>  {{ @$contact['email']}} <br />
                    <span class="fw-bold">Phone : </span> {{ @$contact['phone']}} <br />
                    <span class="fw-bold">Mobile : </span> {{ @$contact['mobile']}} <br />
                    <span class="fw-bold">Fax : </span> {{ @$contact['fax']}} <br />
                    <span class="fw-bold">Is Authorized Person : </span> {{@@$contact['poa'] ? 'Yes' : 'No'}} <br />
                  </small>
                  <hr class="my-2">
                  <span class="d-flex">
                    <a class="me-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#approval-modal">View</a>
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
</div>

<!-- Add New Credit Card Modal -->
<div class="modal fade" id="modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="approval-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        {{-- <h5 class="modal-title" id="exampleModalLabel3">Modal title</h5> --}}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @include('admin.pages.company.approval-request.vertical.tabs.details', ['body_col' => 12])
      </div>
    </div>
  </div>
</div>
<!--/ Add New Credit Card Modal -->

