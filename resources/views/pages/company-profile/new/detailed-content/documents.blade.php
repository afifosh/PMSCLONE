<div class="card-body pt-0">
  <hr>
  <div class="row">
    {!! Form::open(['route' => ['company.kyc-documents.update', 'kyc_document' => 0],'files' => true, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
    @forelse ($requestable_documents as $document)
    @php
      $doc = $approved_documents->where('kyc_doc_id', $document->id)->first();
      $disapprovals = [];
      $modifications = [];
      if($doc){
        $status = 'approved';
        $doc_org = $doc;
        if(@$doc->modifications[0]){
          $status = 'Partially Approved';
          $modifications = transformModifiedData($doc_org->modifications[0]->modifications);
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
        $mod_doc = $doc;
        $doc = transformModifiedData($mod_doc->modifications);
        if($mod_doc->disapprovals->count()){
          $disapprovals = $mod_doc->disapprovals;
          $status = 'rejected';
        }else{
          $status = 'pending';
        }
        $id = Form::hidden('modification_id_' . $document->id, $mod_doc->id);
      }
    @endphp
    {{ $id }}
    <div class="row">
      @if ($loop->index != 0)
        <hr class="my-3">
      @endif
      <div class="d-flex">
        <h5>{{$document->title}}</h5>
        <span class="badge bg-label-{{ getCompanyStatusColor($status) }} ms-3 align-self-start">
          {{ ucwords($status) }}
        </span>
      </div>
      <div class="row">
        @forelse ($disapprovals as $disapproval)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>{{$disapproval->reason}}</strong>
        </div>
        @empty
        @endforelse
      </div>
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
                        class="form-control" @if ($field['type'] == 'file') accept=".jpg,.jpeg,.png" @endif value="{{$doc['fields']['doc_'.$document->id.'_field_'.$index.'_'.$field['type']]}}" required>
                    @modificationAlert(@$modifications['fields']['doc_'.$document->id.'_field_'.$index.'_'.$field['type']] != @$doc_org['fields']['doc_'.$document->id.'_field_'.$index.'_'.$field['type']])
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
