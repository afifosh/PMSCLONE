
@extends('admin/layouts/layoutMaster')

@section('title', 'Pending Documents')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
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
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>

<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script src="{{asset('assets/js/custom/admin-contracts-doc-upload.js')}}"></script>
@endsection

@section('content')
@include('admin.pages.contracts.header', ['tab' => 'pending-documents'])
<div class="card mt-3">
  <div class="card-header">
    <h4 class="card-title">Pending Documents</h4>
  </div>
  <div class="card-body">
    <div class="col-12 mb-4">
      <div class="bs-stepper wizard-vertical vertical mt-2">
        <div class="bs-stepper-header">
          @forelse ($documents as $document)
            {{-- @php
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
            @endphp --}}
            <div class="step step-index-{{$loop->index}}" data-target="#kyc-docs-{{$document['id']}}" data-href="{{route('admin.contracts.pending-documents.index', ['contract' => $contract->id ,'document_id' => $document->id, 'fields_only' => true])}}">
              <button type="button" class="step-trigger">
                <span class="bs-stepper-circle" style="background-color: #FF9F43 !important">{{$loop->iteration}}</span>
                <span class="bs-stepper-label">
                  <span class="bs-stepper-title">{{$document->title}}</span>
                  <span class="bs-stepper-subtitle">{{$document->description}}</span>
                </span>
              </button>
            </div>
          @empty
          @endforelse
        </div>
        <div class="bs-stepper-content">
          @forelse ($documents as $document)
            <div id="kyc-docs-{{$document['id']}}" class="content">
              @if ($document->id == request()->document_id)
                @include('admin.pages.contracts.pending-documents.fields');
              @endif
            </div>
          @empty
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

