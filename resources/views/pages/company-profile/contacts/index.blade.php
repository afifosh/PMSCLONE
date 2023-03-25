@include('pages.company-profile.header-component', ['head_title' => 'Contact Persons', 'head_sm' => 'Manage Your Contacts'])
<div class="row mb-3">
  @forelse ($contacts as $contact)
    <div class="col-md-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <h6 class="fw-semibold mb-0">{{$contact['first_name']}} {{$contact['last_name']}} ({{$contact['position']}})</h6>
            <span class="badge bg-label-{{$contact['id'] ? 'primary' : 'warning'}}">{{$contact['id'] ? 'Approved' : 'Pending Approval'}}</span>
          </span>
          <span class="custom-option-body">
            <small>Email : {{$contact['email']}}<br /> Phone : {{$contact['phone']}}</small>
            <hr class="my-2">
            <span class="d-flex">
              <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Contact Person" data-href="{{route('company.contacts.show', $contact['id'])}}">View</a>
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
    <div class="col-md-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <h6 class="fw-semibold mb-0">{{$contact['first_name']}} {{$contact['last_name']}} ({{$contact['position']}})</h6>
            <span class="badge bg-label-{{@$contact['id'] ? 'primary' : 'warning'}}">{{@$contact['id'] ? 'Approved' : 'Pending Approval'}}</span>
          </span>
          <span class="custom-option-body">
            <small>Email : {{$contact['email']}}<br /> Phone : {{$contact['phone']}}</small>
            <hr class="my-2">
            <span class="d-flex">
              <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Contact Person" data-href="{{route('company.contacts.show', ['contact' => $pending_creation_contact->id, 'type' => 'pending_creation'])}}">View</a>
              @if(auth()->user()->company->isEditable())
                <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.contacts.edit', ['contact' => $pending_creation_contact->id, 'type' => 'pending_creation'])}}">Edit</a>
                <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.contacts.destroy', ['contact' => $pending_creation_contact->id, 'type' => 'pending_creation']) }}">Remove</a>
              @endif
            </span>
          </span>
        </label>
      </div>
    </div>
  @empty
  @endforelse
  @if (!$contacts->count() && !$pending_creation_contacts->count())
  <div class="col-12">
    <div class="mx-auto text-center">
      <div class="my-5">
        <i class="fa fa-magnifying-glass fa-7x" style="color: #cd545b;"></i>
        <h3>No Contact Found!</h3>
        <span>Looks like you have not added any contact person yet. <br> No Worries click the add new button to add a new contact person</span>
      </div>
    </div>
  </div>
  @endif
</div>
<div class="col-12 d-flex justify-content-between">
  <button class="btn btn-label-secondary btn-prev" type="button"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
    <span class="align-middle d-sm-inline-block d-none">Previous</span>
  </button>
  <div>
    @if (auth()->user()->company->isEditable())
      <button type="button" class="btn btn-label-primary me-2" data-toggle="ajax-modal" data-title="Add New Contact Person" data-href="{{route('company.contacts.create')}}">Add new</button>
    @endif
    <button class="btn btn-primary btn-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
  </div>
</div>
