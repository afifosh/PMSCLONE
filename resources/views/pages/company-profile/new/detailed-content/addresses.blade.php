<div class="content-header mb-3 d-sm-flex justify-content-between">
  <div>
    <h6 class="mb-0">Company Addresses</h6>
    <small>Manage Company Addresses</small>
  </div>
  <div>
    @if (auth()->user()->company->isEditable())
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="Add New Contact Person" data-href="{{ route('company.addresses.create') }}">Add New Address</button>
      </div>
    @endif
  </div>
</div>
<hr>
<div class="row">
  @forelse ($addresses as $address)
    @php
      $address_original = $address;
      if ($address->modifications->count()) {
        $address = transformModifiedData($address->modifications[0]->modifications) + $address->toArray();
      }
    @endphp
    <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <span>
              <h6 class="fw-semibold mb-0">{{ $address['name'] }}</h6>
            </span>
            <span class="badge bg-label-{{(!$address_original->modifications->count() && $address['id']) ? 'primary' : 'warning'}}">
              {{$address['id'] ? ($address_original->modifications->count() ? 'Partially Approved' : 'Approved') : 'Pending Approval'}}
            </span>
          </span>
          <span class="custom-option-body">
            <small>
              <span> {{ $address['address_line_1'] }} </span><br />
              <span> {{ $address['address_line_2'] }} </span><br />
              <span> {{ $address['address_line_3'] }}</span><br />
              <span class="fw-bold">Post Code :</span>  {{ $address['postal_code'] }} <br />
              <span class="fw-bold">City : </span> {{ $address['city'] }} <br />
              <span class="fw-bold">State : </span> {{ $address['state'] }} <br />
              <span class="fw-bold">Country : </span> {{ $countries[$address['country_id']] }} <br />
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
    <div class="col-lg-3 col-md-4 col-sm-6 mb-md-3">
      <div class="form-check custom-option custom-option-basic">
        <label class="form-check-label custom-option-content">
          <span class="custom-option-header mb-2">
            <span>
              <h6 class="fw-semibold mb-0">{{ $address['name'] }}</h6>
            </span>
            <span class="badge bg-label-{{@$address['id'] ? 'primary' : 'warning'}}">
              {{@$address['id'] ? 'Approved' : ($pending_addresse->disapprovals()->count() ? 'Rejected': 'Pending Approval')}}
            </span>
          </span>
          <span class="custom-option-body">
            <small>
              <span> {{ $address['address_line_1'] }} </span><br />
              <span> {{ $address['address_line_2'] }} </span><br />
              <span> {{ $address['address_line_3'] }}</span><br />
              <span class="fw-bold">Post Code :</span>  {{ $address['postal_code'] }} <br />
              <span class="fw-bold">City : </span> {{ $address['city'] }} <br />
              <span class="fw-bold">State : </span> {{ $address['state'] }} <br />
              <span class="fw-bold">Country : </span> {{ $countries[$address['country_id']] }} <br />
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
