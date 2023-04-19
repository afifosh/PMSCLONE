<style>
  .cursor-pointer {
    cursor: pointer;
  }
</style>
@if ($isPendingProfile)
  @includeWhen($isPendingProfile, 'pages.company-profile.header-component', ['head_title' => 'KYC Documents', 'head_sm' => 'Please Provide KYC Documents'])
  <hr class="my-3">
@endif
{{-- <div class="d-md-flex justify-content-between">
  <div class="ms-md-2 border-end col-md-2">
    <div class="me-md-3">
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
    <div class="row">
      <div class="d-flex justify-content-between">
        <div>
          <h5 class="mb-0">{{$document->title}}</h5>
          <span>{{$document->description}}</span>
        </div>
        <div>
          <span class="badge bg-label-{{ getCompanyStatusColor($status) }} ms-3 align-self-start">
            {{ ucwords($status) }}
          </span>
        </div>
      </div>
      {!! Form::hidden('document_id', $document->id) !!}
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
            @else
                <div class="form-group col-12 mt-2">
                    <label for="fields_{{ $loop->index }}" class="required">{{ $field['label'] }}</label>
                    <input type="{{ $field['type'] }}" name="fields[{{$field['id']}}]" value="{{@$field['value']}}" id="fields_{{ $loop->index }}"
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
    @if (auth()->user()->company->isEditable())
      <div class="d-flex justify-content-end mt-2">
        <button class="btn btn-primary" data-form="ajax-form" type="button">Submit</button>
      </div>
    @endif
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
</div> --}}

<div class="col-12 mb-4">
  <div class="bs-stepper wizard-vertical vertical mt-2">
    <div class="bs-stepper-header">
      @forelse ($documents as $document)
        <div class="step step-index-{{$loop->index}}" data-target="#kyc-docs-{{$document['id']}}" data-href="{{route('company.kyc-documents.index', ['document_id' => $document->id, 'fields_only' => true])}}">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle">{{$loop->iteration}}</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title">{{$document->title}}</span>
              <span class="bs-stepper-subtitle">{{$document->description}}</span>
            </span>
          </button>
        </div>
        <div class="line"></div>
      @empty
      @endforelse
    </div>
    <div class="bs-stepper-content">
      @forelse ($documents as $document)
        <div id="kyc-docs-{{$document['id']}}" class="content">
          @if ($document->id == request()->document_id)
            @include('pages.company-profile.document.fields');
          @endif
        </div>
      @empty
      @endforelse
    </div>
  </div>
</div>
