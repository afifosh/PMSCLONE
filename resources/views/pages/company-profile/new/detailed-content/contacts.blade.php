<div class="card-body pt-0">
  <hr>
  <div class="row">
    @forelse ($contacts as $contact)
      @php
        $contact_original = $contact;
        if ($contact->modifications->count()) {
          $contact = transformModifiedData($contact->modifications[0]->modifications) + $contact->toArray();
        }
      @endphp
      <div class="col-sm-6 mb-md-3">
        <div class="form-check custom-option custom-option-basic">
          <label class="form-check-label custom-option-content">
            <span class="custom-option-header mb-2">
              <span>
                <h6 class="fw-semibold mb-0">{{$contact['first_name']}} {{$contact['last_name']}}</h6>
                <small>{{$contact['position']}}</small>
              </span>
              <span class="badge bg-label-{{(!$contact_original->modifications->count() && $contact['id']) ? 'success' : 'warning'}}">
                {{$contact['id'] ? ($contact_original->modifications->count() ? 'Partially Approved' : 'Approved') : 'Pending Approval'}}
              </span>
            </span>
            <span class="custom-option-body">
              <small>
                <span class="fw-bold">Type :</span>  {{$contact['type'] == 1 ? 'Owner' : 'Employee'}} <br />
                <span class="fw-bold">Email :</span>  {{ $contact['email']}} <br />
                <span class="fw-bold">Phone : </span> {{ $contact['phone']}} <br />
                <span class="fw-bold">Mobile : </span> {{ $contact['mobile']}} <br />
                <span class="fw-bold">Fax : </span> {{ $contact['fax']}} <br />
                <span class="fw-bold">Is Authorized Person : </span> {{@$contact['poa'] ? 'Yes' : 'No'}} <br />
              </small>
              <hr class="my-2">
              <span class="d-flex">
                {{-- <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Contact Person" data-href="{{route('company.contacts.show', $contact['id'])}}">View</a> --}}
                @if(auth()->user()->company->isEditable())
                  <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.contacts.edit', $contact['id'])}}">Edit</a>
                  <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.contacts.destroy', $contact['id']) }}">Remove</a>
                @endif
              </span>
            </span>
          </label>
        </div>
      </div>
    @empty
    @endforelse
    @forelse ($pending_creation_contacts as $pending_creation_contact)
      @php
          $contact = transformModifiedData($pending_creation_contact->modifications);
      @endphp
      <div class="col-sm-6 mb-md-3">
        <div class="form-check custom-option custom-option-basic">
          <label class="form-check-label custom-option-content">
            <span class="custom-option-header mb-2">
              <span>
                <h6 class="fw-semibold mb-0">{{$contact['first_name']}} {{$contact['last_name']}}</h6>
                <small>{{$contact['position']}}</small>
              </span>
              <span class="badge bg-label-{{@$contact['id'] ? 'success' : ($pending_creation_contact->disapprovals()->count() ? 'danger': 'warning')}}">
                {{@$contact['id'] ? 'Approved' : ($pending_creation_contact->disapprovals()->count() ? 'Rejected': 'Pending Approval')}}
              </span>
            </span>
            <span class="custom-option-body">
              <small>
                <span class="fw-bold">Type :</span>  {{$contact['type'] == 1 ? 'Owner' : 'Employee'}} <br />
                <span class="fw-bold">Email :</span>  {{$contact['email']}} <br />
                <span class="fw-bold">Phone : </span> {{$contact['phone']}} <br />
                <span class="fw-bold">Mobile : </span> {{$contact['mobile']}} <br />
                <span class="fw-bold">Fax : </span> {{$contact['fax']}} <br />
                <span class="fw-bold">Is Authorized Person : </span> {{@$contact['poa'] ? 'Yes' : 'No'}} <br />
              </small>
              @if(auth()->user()->company->isEditable())
                <hr class="my-2">
                <span class="d-flex">
                  {{-- <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Contact Person" data-href="{{route('company.contacts.show', ['contact' => $pending_creation_contact->id, 'type' => 'pending_creation'])}}">View</a> --}}
                  <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.contacts.edit', ['contact' => $pending_creation_contact->id, 'type' => 'pending_creation'])}}">Edit</a>
                  <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.contacts.destroy', ['contact' => $pending_creation_contact->id, 'type' => 'pending_creation']) }}">Remove</a>
                </span>
              @endif
            </span>
          </label>
        </div>
      </div>
    @empty
    @endforelse
    @if (auth()->user()->company->isEditable())
      <div class="col-sm-6 mb-md-3">
        <div class="form-check custom-option custom-option-basic h-100">
          <div class="d-flex justify-content-center align-items-center h-100">
            <button class="text-center btn btn-primary" data-toggle="ajax-modal" data-title="Add New Contact Person" data-href="{{ route('company.contacts.create') }}">Add New</button>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
