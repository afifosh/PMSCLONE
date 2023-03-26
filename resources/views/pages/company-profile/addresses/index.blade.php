@include('pages.company-profile.header-component', ['head_title' => 'Addresses', 'head_sm' => 'Manage Addresses'])
<div class="row mb-3">
  @forelse ($addresses as $address)
    <div class="col-md-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <h6 class="fw-semibold mb-0">{{$address['name']}}</h6>
            <span class="badge bg-label-primary">{{$address['id'] ? 'Approved' : 'Pending Approval'}}</span>
          </span>
          <span class="custom-option-body">
            <small>{{$address['address_line_1']}}, {{$address['address_line_2']}}, {{$address['address_line_3']}}<br />
              Phone : {{$address['phone']}} <br/>
            </small>
            <hr class="my-2">
            <span class="d-flex">
              <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Address" data-href="{{route('company.addresses.show', $address['id'])}}">View</a>
              @if(auth()->user()->company->isEditable())
                <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Address" data-href="{{route('company.addresses.edit', $address['id'])}}">Edit</a>
                <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.addresses.destroy', $address['id']) }}">Remove</a>
              @endif
            </span>
          </span>
        </label>
      </div>
    </div>
  @empty
  @endforelse
  @forelse ($pending_addresses as $pending_addresse)
  @php
      $address = transformModifiedData($pending_addresse->modifications);
  @endphp
    <div class="col-md-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <h6 class="fw-semibold mb-0">{{$address['name']}}</h6>
            <span class="badge bg-label-{{@$address['id'] ? 'primary' : 'warning'}}">{{@$address['id'] ? 'Approved' : 'Pending Approval'}}</span>
          </span>
          <span class="custom-option-body">
            <small>{{$address['address_line_1']}}, {{$address['address_line_2']}}, {{$address['address_line_3']}}<br />
              Phone : {{$address['phone']}} <br/>
            </small>
            <hr class="my-2">
            <span class="d-flex">
              <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Address" data-href="{{route('company.addresses.show', ['address' => $pending_addresse->id, 'type' => 'pending_creation'])}}">View</a>
              @if(auth()->user()->company->isEditable())
                <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Address" data-href="{{route('company.addresses.edit', ['address' => $pending_addresse->id, 'type' => 'pending_creation'])}}">Edit</a>
                <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.addresses.destroy', ['address' => $pending_addresse->id, 'type' => 'pending_creation']) }}">Remove</a>
              @endif
              </span>
            </span>
        </label>
      </div>
    </div>
  @empty
  @endforelse

  @if (!$addresses->count() && !$pending_addresses->count())
  <div class="col-12">
    <div class="mx-auto text-center">
      <div class="my-5">
        <i class="fa fa-magnifying-glass fa-7x" style="color: #cd545b;"></i>
        <h3>No Address Found!</h3>
        <span>Looks like you have not added any address yet. <br> No Worries click the add new button to add a new address</span>
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
      <button type="button" class="btn btn-label-primary me-2" data-toggle="ajax-modal" data-title="Add New Address" data-href="{{route('company.addresses.create')}}">Add new address</button>
    @endif
    <button class="btn btn-primary btn-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
  </div>
</div>