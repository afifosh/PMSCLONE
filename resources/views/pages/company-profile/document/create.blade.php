@include('pages.company-profile.header-component', ['head_title' => 'KYC Documents', 'head_sm' => 'Please Provide KYC Documents'])
{!! Form::open(['route' => 'company.kyc-documents.store', 'files' => true]) !!}
@forelse ($documents as $document)
<div class="row">
  <hr class="my-3">
  <h5>{{$document->title}}</h5>
    @foreach ($document->fields as $index => $field)
        @if ($field['type'] == 'textarea')
            <div class="form-group col-6">
                <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
                <textarea name="doc_{{$document->id}}_field_{{$loop->index}}_{{$field['type']}}" id="fields_{{ $loop->index }}" class="form-control h-25" required></textarea>
            </div>
        @else
            <div class="form-group col-6">
                <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
                <input type="{{ $field['type'] }}" name="doc_{{$document->id}}_field_{{$loop->index}}_{{$field['type']}}" id="fields_{{ $loop->index }}"
                    class="form-control" @if ($field['type'] == 'file') accept=".jpg,.jpeg,.png" @endif required>
            </div>
        @endif
    @endforeach
</div>
@empty
@endforelse
<div class="col-12 d-flex justify-content-between mt-3">
  <button class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
    <span class="align-middle d-sm-inline-block d-none">Previous</span>
  </button>
  <div>
    <button class="btn btn-primary submit-and-next" type="button"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
    <button type="button" data-form="ajax-form" class="d-none"></button>
  </div>
</div>
{!! Form::close() !!}
