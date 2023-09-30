
  {!! Form::open(['route' => ['admin.contracts.pending-documents.store', ['contract' => $contract]], 'files' => true]) !!}

  {!! Form::hidden('document_id', $document->id) !!}
  <div class="row g-3">
    @foreach ($document->fields as $index => $field)
      @forelse (@$doc['fields'] ?? [] as $submited_field)
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
        <div class="dropzone needsclick" data-upload-url="{{ route('admin.contracts.upload-requested-doc', [$contract])}}" data-response="#{{'fields_'.$field['id']}}" data-file-path="{{@$field['value'] ? getAssetUrl(@$field['value']) : ''}}">
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
        <input type="date" name="expiry_date" value="{{@$doc['expiry_date'] ? date('Y-m-d', strtotime($doc['expiry_date'])) : ''}}" class="form-control flatpickr">
      </div>
    @endif
      <div class="col-12 d-flex justify-content-end">
        <button type="button" class="btn btn-primary" data-form="ajax-form"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
      </div>
  </div>
{!! Form::close() !!}
