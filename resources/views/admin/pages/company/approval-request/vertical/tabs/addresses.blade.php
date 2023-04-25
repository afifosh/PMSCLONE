<div class="card w-100">
  <div class="card-body">
    <h5>Company Addresses</h5>
      <hr>
    <div class="row">
      @forelse ($approved_addresses as $address)
      @php
        $address_original = $address;
        $modifications = [];
        $isEditable = false;
        $status = 'approved';
        $approvals = @$address_original->modifications[0]->approvals ?? [];
        $disapprovals = @$address_original->modifications[0]->disapprovals ?? [];
        if ($address->modifications->count()) {
          if($address_original->modifications[0]->approvals->count() < $level && !$address_original->modifications[0]->disapprovals->count()){
            $isEditable = true;
            $status = 'partially approved';
            $modifications = transformModifiedData($address_original->modifications[0]->modifications);
            $address['modification_id'] = $address_original->modifications[0]->id;
          }
          $address = transformModifiedData($address_original->modifications[0]->modifications) + $address->toArray();
        }
        if ($address_original->modifications->count() && $address_original->modifications[0]->disapprovals->count()) {
          $status = 'rejected';
        }
      @endphp
      @include('admin.pages.company.approval-request.vertical.tabs.components.address', ['address' => $address])
      @php
          unset($modifications, $address, $address_original);
      @endphp
      @empty
      @endforelse
      @forelse ($addresses as $address)
        @php
          $address_original = $address;
          $isEditable = true;
          $approvals = $address_original->approvals;
          $disapprovals = $address_original->disapprovals;
          $status = 'pending';
          if(isset($address) && ($address->approvals_count >= $level || $address->disapprovals_count)) {
              $isEditable = false;
              if($address->approvals_count >= $level){
                $status = 'approved';
              }elseif ($address->disapprovals_count) {
                $status = 'rejected';
              }
          }
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
