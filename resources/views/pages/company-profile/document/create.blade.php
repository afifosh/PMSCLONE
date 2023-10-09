<style>
  .cursor-pointer {
    cursor: pointer;
  }
  .bs-stepper .step.active .bs-stepper-circle {
    opacity: 1 !important;
  }
  .light-style .bs-stepper .bs-stepper-header .step:not(.active) .bs-stepper-circle {
    opacity: 0.3 !important;
  }
  .bs-stepper .step.crossed .step-trigger .bs-stepper-circle {
    background-color: inherit !important;
    color: #474747 !important;
  }
</style>
@if ($isPendingProfile)
  @includeWhen($isPendingProfile, 'pages.company-profile.header-component', ['head_title' => 'KYC Documents', 'head_sm' => 'Please Provide KYC Documents'])
  <hr class="my-3">
@endif
<div class="col-12 mb-4">
  <div class="bs-stepper wizard-vertical vertical mt-2 shadow-none">
    <div class="bs-stepper-header">
      @forelse ($documents as $document)
        @php
          $doc = $approved_documents->where('kyc_doc_id', $document->id)->first();
          if($doc){
            $status = 'approved';
            if(@$doc->modifications[0]){
              $status = 'Partially Approved';
              if($doc->modifications[0]->disapprovals->count()){
                $status = 'rejected';
              }
            }
          }
          if(!$doc){
            $doc = auth()->user()->company->POCKycDoc()->whereJsonContains('modifications->kyc_doc_id->modified', $document->id)->first();
            if($doc && $doc->disapprovals->count()){
              $status = 'rejected';
            }else{
              $status = 'pending';
            }
          }
          $color = $status == 'approved' ? '#28C76F' : ($status == 'rejected' ? '#EA5455' : '#FF9F43') ;
        @endphp
        <div class="step step-index-{{$loop->index}}" data-target="#kyc-docs-{{$document['id']}}" data-href="{{route('company.kyc-documents.index', ['document_id' => $document->id, 'fields_only' => true])}}">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle" style="background-color: {{$color}}!important">{{$loop->iteration}}</span>
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
