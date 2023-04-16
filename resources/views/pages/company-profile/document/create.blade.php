<style>
  .cursor-pointer {
    cursor: pointer;
  }
</style>
@if ($isPendingProfile)
  @includeWhen($isPendingProfile, 'pages.company-profile.header-component', ['head_title' => 'KYC Documents', 'head_sm' => 'Please Provide KYC Documents'])
  <hr class="my-3">
@endif
<div class="d-md-flex justify-content-between">
  <div class="ms-md-2 border-end col-md-2">
    <div class="me-md-3">
        {{-- @php
          request()->show_doc = request()->show_doc ?? $documents[0]->id;
        @endphp --}}
        @forelse ($documents as $document)
          <a data-href="{{ route('company.kyc-documents.index', ['document_id' => $document->id])}}" data-toggle-view="#company-documents" class="border d-flex fw-bold mt-2 p-2 cursor-pointer {{ request()->document_id == $document->id ? 'bg-label-primary' : ''}}">
              <span> {{$loop->iteration}}). {{$document->title}} </span>
          </a>
        @empty
        @endforelse
    </div>
  </div>
  <div class="ms-3 w-100">
    {!! Form::open(['route' => 'company.kyc-documents.store', 'files' => true]) !!}
    @forelse ($documents->where('id', request()->document_id) as $document)
    <div class="row">
      <h5>{{$document->title}}</h5>
      {!! Form::hidden('document_id', $document->id) !!}
        @foreach ($document->fields as $index => $field)
            @if ($field['type'] == 'textarea')
                <div class="form-group col-12 mt-2">
                    <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
                    <textarea name="fields[{{$field['id']}}]" id="fields_{{ $loop->index }}" class="form-control h-25" required></textarea>
                </div>
            @else
                <div class="form-group col-12 mt-2">
                    <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
                    <input type="{{ $field['type'] }}" name="fields[{{$field['id']}}]" id="fields_{{ $loop->index }}"
                        class="form-control" @if ($field['type'] == 'file') accept=".jpg,.jpeg,.png" @endif required>
                </div>
            @endif
        @endforeach
        @if ($document->is_expirable)
          <div class="form-group col-12 mt-2">
            <label for="" class="required">{{ $document->expiry_date_title }}</label>
            <input type="date" name="expiry_date" class="form-control">
          </div>
        @endif
    </div>
    @empty
    @endforelse
    <div class="d-flex justify-content-end mt-2">
      <button class="btn btn-primary" data-form="ajax-form" type="button">Submit</button>
    </div>
    @if(@$isPendingProfile)
      <hr class="my-3">
      <div class="col-12 d-flex justify-content-between mt-3">
        <button class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
          <span class="align-middle d-sm-inline-block d-none">Previous</span>
        </button>
        <div>
          <button class="btn btn-primary" onclick="triggerNext();" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
        </div>
      </div>
    @endif
    {!! Form::close() !!}
  </div>
</div>
