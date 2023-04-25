<div class="card w-100">
  <div class="card-body">
    <h5>Contact Persons</h5>
      <hr>
      <div class="row">
        @forelse ($approved_contacts as $contact)
        @php
          $contact_original = $contact;
          $modifications = [];
          $isEditable = false;
          $status = 'approved';
          $approvals = [];
          $disapprovals = [];
          if ($contact->modifications->count()) {
            $approvals = $contact_original->modifications[0]->approvals;
            $disapprovals = $contact_original->modifications[0]->disapprovals;
            if($contact_original->modifications[0]->approvals->count() < $level && !$contact_original->modifications[0]->disapprovals->count()){
              $isEditable = true;
              $status = 'partially approved';
              $modifications = transformModifiedData($contact_original->modifications[0]->modifications);
              $contact['modification_id'] = $contact_original->modifications[0]->id;
            }
            $contact = transformModifiedData($contact_original->modifications[0]->modifications) + $contact->toArray();
          }
          if ($contact_original->modifications->count() && $contact_original->modifications[0]->disapprovals->count()) {
            $status = 'rejected';
          }
        @endphp
        @include('admin.pages.company.approval-request.vertical.tabs.components.contact-person', ['contact' => $contact])
        @php
          unset($modifications, $contact, $contact_original);
        @endphp
        @empty
        @endforelse

        @forelse ($contacts as $contact)
          @php
            $contact_original = $contact;
            $isEditable = true;
            $status = 'pending';
            $approvals = $contact_original->approvals;
            $disapprovals = $contact_original->disapprovals;
            if(isset($contact) && ($contact->approvals_count >= $level || $contact->disapprovals_count)) {
                $isEditable = false;
                if($contact->approvals_count >= $level){
                  $status = 'approved';
                }elseif ($contact->disapprovals_count) {
                  $status = 'rejected';
                }
            }
            $contact = transformModifiedData($contact->modifications);
            $contact['modification_id'] = $contact_original->id;
          @endphp
          @include('admin.pages.company.approval-request.vertical.tabs.components.contact-person', ['contact' => $contact])
        @empty
        @endforelse
      </div>
      <div class="row">
        <div class="col-12 d-flex justify-content-between">
          <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'details']) }}" class="btn btn-light">Previews</a>
          <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'addresses']) }}" class="btn btn-primary">Next</a>
        </div>
      </div>
    </div>
  </div>
</div>

