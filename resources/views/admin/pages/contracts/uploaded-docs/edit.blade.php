@if($modelInstance instanceof \App\Models\Contract)
  {!! Form::open(['route' => ['admin.contracts.uploaded-documents.update', ['contract' => $modelInstance->id, $uploaded_doc]], 'method' => 'PUT', 'files' => true]) !!}
@else
  {!! Form::open(['route' => ['admin.invoices.uploaded-documents.update', ['invoice' => $modelInstance->id, $uploaded_doc]], 'method' => 'PUT', 'files' => true]) !!}
@endif

{{-- Requested Doc Id --}}
{!! Form::hidden('document_id', $document->id) !!}
{{-- Uploaded Doc Id --}}
{!! Form::hidden('uploaded_doc_id', $uploaded_doc->id) !!}
<div class="row g-3">
  @foreach ($document->fields as $index => $field)
    @forelse (@$uploaded_doc['fields'] ?? [] as $submited_field)
      @php
        if($submited_field['id'] == $field['id']){
          $field['value'] = $submited_field['value'];
        }
      @endphp
    @empty
    @endforelse
    @if ($field['type'] == 'textarea')
      <div class="form-group col-12 mt-2">
        <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
        <textarea name="fields[{{$field['id']}}]" id="fields_{{ $loop->index }}" class="form-control h-25" required></textarea>
      </div>
    @elseif($field['type'] == 'file')
    <div class="col-12 py-3">
      <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
      <div class="dropzone needsclick" data-upload-url="{{ ($modelInstance instanceof \App\Models\Contract ? route('admin.contracts.upload-requested-doc', [$modelInstance->id]) : route('admin.invoices.upload-requested-doc', [$modelInstance->id]))}}" data-response="#{{'fields_'.$field['id']}}" data-file-path="{{@$field['value'] ? getAssetUrl(@$field['value']) : ''}}">
        <div class="dz-message needsclick">
          <small class="h6"> Drag and Drop the {{$field['label']}} here or click to upload </small>
        </div>
        <div class="fallback">
          <input name="file" type="file" />
        </div>
      </div>
    </div>
    {!! Form::hidden('fields['.$field['id'].']', @$field['value'] ?? null, ['id' => 'fields_'.$field['id']]) !!}
    {!! Form::hidden('field_is_new['.$field['id'].']', null, ['id' => 'fields_'.$field['id'].'is_new']) !!}
    @else
      <div class="form-group col-12 mt-2">
        <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
        <input type="{{ $field['type'] }}" name="fields[{{$field['id']}}]" value="{{@$field['value']}}" id="fields_{{ $loop->index }}"
            class="form-control {{$field['type'] == 'date' ? 'flatpickr' : ''}}" @if ($field['type'] == 'file') accept=".jpg,.jpeg,.png" @endif required>
      </div>
    @endif
  @endforeach
  @if ($document->is_expirable)
    <div class="form-group col-12 mt-2">
      <label for="" class="required">{{ $document->expiry_date_title }}</label>
      <input type="date" name="expiry_date" value="{{@$uploaded_doc['expiry_date'] ? date('Y-m-d', strtotime($uploaded_doc['expiry_date'])) : ''}}" class="form-control flatpickr">
    </div>
  @endif
    <div class="col-12 d-flex justify-content-end">
      <button type="button" class="btn btn-primary" data-form="ajax-form">Update</button>
    </div>
</div>
{!! Form::close() !!}
