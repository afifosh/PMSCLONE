<div class="content-header mb-3">
  <h6 class="mb-0">Contacts</h6>
  <small>Add Your Contact Persons</small>
</div>
<form action="{{route('company.updateContacts')}}" method="POST">
  @csrf
  <div class="row g-3 form-repeater">
    <div data-repeater-list="contacts">
      @forelse ($contacts as $contact)
        <div class="p-3 mt-4 border rounded position-relative" data-repeater-item style="background-color: #f1f0f2;">
            <button class="btn btn-xs rounded-circle  btn-label-danger position-absolute top-0 start-100 translate-middle" type="button" data-repeater-delete>
              <i class="my-2 ti ti-trash ti-xs"></i>
            </button>
          <div class="row">
            {!! Form::hidden('contacts[][id]', $contact['id']) !!}
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">Contact Type</label>
              {!! Form::select('contacts[][type]', ['Owner', 'Employee'], $contact['type'], ['class' => 'form-select select2']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">Title</label>
              {!! Form::text('contacts[][title]', $contact['title'], ['class' => 'form-control', 'placeholder' => 'Title']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">First Name</label>
              {!! Form::text('contacts[][first_name]', $contact['first_name'], ['class' => 'form-control', 'placeholder' => 'First Name']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">Last Name</label>
              {!! Form::text('contacts[][last_name]', $contact['last_name'], ['class' => 'form-control', 'placeholder' => 'Last Name']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">Position</label>
              {!! Form::text('contacts[][position]', $contact['position'], ['class' => 'form-control', 'placeholder' => 'Position']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">Phone</label>
              {!! Form::text('contacts[][phone]', $contact['phone'], ['class' => 'form-control', 'placeholder' => 'Phone']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">Mobile</label>
              {!! Form::text('contacts[][mobile]', $contact['mobile'], ['class' => 'form-control', 'placeholder' => 'Mobile']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">Fax</label>
              {!! Form::text('contacts[][fax]', $contact['fax'], ['class' => 'form-control', 'placeholder' => 'Fax']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-4 col-12 mb-0">
              <label class="form-label">Email</label>
              {!! Form::email('contacts[][email]', $contact['email'], ['class' => 'form-control', 'placeholder' => 'Email']) !!}
            </div>
            <div class="mb-1 col-lg-6 col-xl-6 col-12 mb-0">
              <label>POA Letter</label>
              {!! Form::file('poa', ['class' => 'form-control']) !!}
            </div>
          </div>
        </div>
      @empty
      @endforelse
    </div>
    <div class="mb-0 text-end">
      <button class="btn btn-primary" type="button" data-repeater-create>
        <i class="ti ti-plus me-1"></i>
        <span class="align-middle">Add</span>
      </button>
    </div>
    <input class="d-none" type="text" name="submit_type">
    <div class="col-12 d-flex justify-content-between">
      <button class="btn btn-label-secondary btn-prev" type="button"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
        <span class="align-middle d-sm-inline-block d-none">Previous</span>
      </button>
      <div>
        @if (!auth()->user()->company->contacts->count())
          <button class="btn btn-outline-secondary save-draft" type="button">Save Draft</button>
        @endif
        <button class="btn btn-primary btn-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
        <button type="button" data-form="ajax-form" class="d-none"></button>
      </div>
    </div>
  </div>
</form>
