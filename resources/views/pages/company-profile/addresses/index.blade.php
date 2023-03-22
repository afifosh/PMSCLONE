<div class="content-header mb-3">
  <h6 class="mb-0">Addresses</h6>
  <small>Manage Addresses</small>
</div>
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
              <a class="me-2" href="javascript:void(0)" data-toggle="ajax-modal" data-title="Edit Contact Person" data-href="{{route('company.addresses.edit', $address['id'])}}">Edit</a>
              <a href="javascript:void(0)" data-toggle="ajax-delete" data-href="{{ route('company.addresses.destroy', $address['id']) }}">Remove</a>
            </span>
          </span>
        </label>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="alert alert-warning">
        <i class="ti ti-alert me-2"></i> No addresses found.
      </div>
    </div>
  @endforelse
</div>
<div class="col-12 d-flex justify-content-between">
  <button class="btn btn-label-secondary btn-prev" type="button"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
    <span class="align-middle d-sm-inline-block d-none">Previous</span>
  </button>
  <div>
    <button type="button" class="btn btn-label-primary me-2" data-toggle="ajax-modal" data-title="Add New Address" data-href="{{route('company.addresses.create')}}">Add new address</button>
    <button class="btn btn-primary btn-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
    <button type="button" data-form="ajax-form" class="d-none"></button>
  </div>
</div>
