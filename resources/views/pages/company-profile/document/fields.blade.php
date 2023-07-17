@php
  $doc = $approved_documents->where('kyc_doc_id', $document->id)->first();
  $disapprovals = [];
  $modifications = [];
  $type = 'new';
  if($doc){
    $type = 'modification';
    $status = 'approved';
    $doc_org = $doc;
    if(@$doc->modifications[0]){
      $status = 'Partially Approved';
      $modifications = transformModifiedData($doc_org->modifications[0]->modifications);
      $modifications['modified_fields'] = collectModifiedFields($doc_org->modifications[0]->modifications);
      $doc = $modifications + $doc->toArray();
      if($doc_org->modifications[0]->disapprovals->count()){
        $disapprovals = $doc_org->modifications[0]->disapprovals;
        $status = 'rejected';
      }
    }
    $id = Form::hidden('doc_id_' . $document->id, $doc_org->id);
  }
  if(!$doc){
    $doc = auth()->user()->company->POCKycDoc()->whereJsonContains('modifications->kyc_doc_id->modified', $document->id)->first();
    if($doc){
      $type = 'pending_creation';
    }
    $doc = $doc ? $doc : [];
    $mod_doc = $doc;
    $doc = $doc ? transformModifiedData($mod_doc->modifications) : [];
    if($mod_doc && $mod_doc->disapprovals->count()){

      $disapprovals = $mod_doc->disapprovals;
      $status = 'rejected';
    }else{
      $status = 'pending';
    }
    $id = Form::hidden('modification_id_' . $document->id, @$mod_doc->id);
  }
@endphp
@if($type == 'new')
  {!! Form::open(['route' => 'company.kyc-documents.store', 'files' => true]) !!}
@else
  {!! Form::open(['route' => ['company.kyc-documents.update', 'kyc_document' => $document['id']], 'method' => 'PUT', 'files' => true]) !!}
@endif
  {{ $id }}
  <div class="content-header mb-3">
    @forelse ($disapprovals as $disapproval)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>{{$disapproval->reason}}</strong>
    </div>
    @empty
    @endforelse
    <div class="d-flex justify-content-between">
      <div>
        <h6 class="mb-0">{{$document->title}}</h6>
        <small>{{$document->description}}</small>
      </div>
      <div>
        <span class="badge bg-label-{{ getCompanyStatusColor($status) }} ms-3 align-self-start">
          {{ ucwords($status) }}
        </span>
      </div>
    </div>
  </div>
  <hr>
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
      <div class="dropzone needsclick" data-upload-url="{{ route('company.kyc-documents.upload-doc')}}" data-response="#{{'fields_'.$field['id']}}" data-file-path="{{@$field['value'] ? getAssetUrl(@$field['value']) : ''}}">
        <div class="dz-message needsclick">
          <small class="h6"> Drag and Drop the {{$field['label']}} here or click to upload </small>
        </div>
        <div class="fallback">
          <input name="file" type="file" />
        </div>
      </div>
      {!! Form::hidden('fields['.$field['id'].']', @$field['value'] ?? null, ['id' => 'fields_'.$field['id']]) !!}
      {!! Form::hidden('field_is_new['.$field['id'].']', null, ['id' => 'fields_'.$field['id'].'is_new']) !!}
      @modificationAlert(@$modifications['modified_fields']['fields'][$field['id']])
      @else
        <div class="form-group col-12 mt-2">
          <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
          <input type="{{ $field['type'] }}" name="fields[{{$field['id']}}]" value="{{@$field['value']}}" id="fields_{{ $loop->index }}"
              class="form-control" @if ($field['type'] == 'file') accept=".jpg,.jpeg,.png" @endif required>
              @modificationAlert(@$modifications['modified_fields']['fields'][$field['id']])
        </div>
      @endif
    @endforeach
    {{-- {{dd(@$doc)}} --}}
    @if ($document->is_expirable)
      <div class="form-group col-12 mt-2">
        <label for="" class="required">{{ $document->expiry_date_title }}</label>
        <input type="date" name="expiry_date" value="{{@$doc['expiry_date'] ? date('Y-m-d', strtotime($doc['expiry_date'])) : ''}}" class="form-control flatpickr">
    {{-- {{dd(@$doc['expiry_date'])}} --}}

      </div>
    @endif
    {{-- @if(auth()->user()->company->isEditable()) --}}
      <div class="col-12 d-flex justify-content-between">
        <button type="button" class="btn btn-label-secondary doc-btn-prev"> <i class="ti ti-arrow-left me-sm-1 me-0"></i>
          <span class="align-middle d-sm-inline-block d-none">Previous</span>
        </button>
        <button type="button" class="btn btn-primary {{auth()->user()->company->isEditable() ? '' : 'disabled'}}" data-form="ajax-form"> <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span> <i class="ti ti-arrow-right"></i></button>
      </div>
    {{-- @endif --}}
  </div>
{!! Form::close() !!}
