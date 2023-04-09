<div class="card w-100">
  <div class="card-body">
    <h5>Contact Persons</h5>
      <hr>
      <div class="row">
        @forelse ($approved_contacts as $contact)
        @include('admin.pages.company.approval-request.vertical.tabs.components.contact-person', ['contact' => $contact])
        @empty
        @endforelse

        @forelse ($contacts as $contact)
          @php
            $contact_original = $contact;
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

