<div class="card-body pt-0">
  <hr>
  <div class="row">
    {!! Form::open(['route' => 'company.kyc-documents.store', 'files' => true]) !!}
    @forelse ($documents as $document)
    <div class="row">
      @if ($loop->index != 0)
        <hr class="my-3">
      @endif
      <h5>{{$document->title}}</h5>
        @foreach ($document->fields as $index => $field)
            @if ($field['type'] == 'textarea')
                <div class="form-group col-6 mt-2">
                    <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
                    <textarea name="doc_{{$document->id}}_field_{{$loop->index}}_{{$field['type']}}" id="fields_{{ $loop->index }}" class="form-control h-25" required></textarea>
                </div>
            @else
                <div class="form-group col-6 mt-2">
                    <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
                    <input type="{{ $field['type'] }}" name="doc_{{$document->id}}_field_{{$loop->index}}_{{$field['type']}}" id="fields_{{ $loop->index }}"
                        class="form-control" @if ($field['type'] == 'file') accept=".jpg,.jpeg,.png" @endif required>
                </div>
            @endif
        @endforeach
    </div>
    @empty
    @endforelse
    @if (auth()->user()->company->isEditable())
      <div class="col-12 d-flex justify-content-end mt-3">
        <div>
          <button class="btn btn-primary submit-and-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Update</span> <i class="ti ti-arrow-right"></i></button>
          <button type="button" data-form="ajax-form" class="d-none"></button>
        </div>
      </div>
    @endif
    {!! Form::close() !!}
  </div>
</div>
