<div class="card w-100">
  <div class="card-body">
    <h5>Company Addresses</h5>
      <hr>
    <div class="row">
      @forelse ($approved_addresses as $address)
      @include('admin.pages.company.approval-request.vertical.tabs.components.address', ['address' => $address])
      @empty
      @endforelse
      @forelse ($addresses as $address)
        @php
          $address_original = $address;
          $address = transformModifiedData($address->modifications);
          $address['modification_id'] = $address_original->id;
        @endphp
         @include('admin.pages.company.approval-request.vertical.tabs.components.address', ['address' => $address])
      @empty
      @endforelse
    </div>
    <div class="row">
      <div class="col-12 d-flex justify-content-between">
        <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'contact-persons']) }}" class="btn btn-light">Previews</a>
        <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'documents']) }}" class="btn btn-primary">Next</a>
      </div>
    </div>
  </div>
</div>
