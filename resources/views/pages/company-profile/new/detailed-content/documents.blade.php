<div class="card-body pt-0">
  <hr class="test">
  <div class="row">
    {{-- {{dd('t')}} --}}
    {{-- {!! Form::open(['route' => ['company.kyc-documents.update', 'kyc_document' => 0],'files' => true, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) !!}
    @php
        request()->document_id = request()->document_id ?? $requestable_documents[0]->id;
    @endphp --}}
    {{-- @forelse ($requestable_documents->where('id', request()->document_id) as $document)
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
        @foreach ($document->fields as $field)
         @foreach($doc['fields'] as $doc_field)
            @if($field['id'] == $doc_field['id'])
              @if ($field['type'] == 'textarea')
                  <div class="form-group col-12 mt-2">
                      <label for="fields_{{ $loop->index }}" class="required">{{ $doc_field['label'] }}</label>
                      <textarea name="doc_{{$document->id}}_field_{{$loop->index}}_{{$field['type']}}" id="fields_{{ $loop->index }}" class="form-control h-25" required></textarea>
                  </div>
              @else
                  <div class="form-group col-12 mt-2">
                      <label for="fields_{{ $loop->index }}" class="required">{{ $doc_field['label'] }}</label>
                      <input type="{{ $field['type'] }}" name="doc_{{$document->id}}_field_{{$loop->index}}_{{$field['type']}}" id="fields_{{ $loop->index }}"
                          class="form-control" @if ($field['type'] == 'file') accept=".jpg,.jpeg,.png" @endif value="{{$doc_field['value']}}" required>
                  </div>
              @endif
            @endif
            @endforeach
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
    @endif --}}
    {{-- {!! Form::close() !!} --}}
  </div>
  <div id="company-documents">
    @include('pages.company-profile.document.create')
  </div>
</div>
