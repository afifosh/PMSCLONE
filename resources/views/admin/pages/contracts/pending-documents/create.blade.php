
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
    color: white !important;
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
@includeWhen(isset($contract),'admin.pages.contracts.header', ['tab' => 'pending-documents'])
@includeWhen(isset($invoice) ,'admin.pages.invoices.header-top', ['tab' => 'pending-documents'])
<div class="card mt-3">
  <div class="card-header">
    <h4 class="card-title">Pending Documents</h4>
  </div>
  <div class="card-body">
    <div class="col-12 mb-4">
      @if (count($documents))
        <div class="bs-stepper wizard-vertical horizontal mt-2 shadow-none">
          @forelse ($documents as $document)
            <!-- HEADER -->
            <div class="bs-stepper-header">
              <div class="step step-index-{{$loop->index}}" data-target="#kyc-docs-{{$document['id']}}" data-href="
                {{ (isset($contract)
                  ? route('admin.contracts.pending-documents.index', ['contract' => $contract->id ,'document_id' => $document->id, 'fields_only' => true])
                  : route('admin.invoices.pending-documents.index', ['invoice' => $invoice->id ,'document_id' => $document->id, 'fields_only' => true])
                )}}
                ">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle" style="background-color: {{in_array($document->id, $expired_documents) ? '#FF9F43' : (in_array($document->id, $valid_documents) ? '#008000' : '#FF0000')}} !important; color:white !important;">{{$loop->iteration}}</span>
                  <span class="bs-stepper-label mw-100">
                    <span class="bs-stepper-title">{{$document->title}}</span>
                    <span class="bs-stepper-subtitle">{{$document->description}}</span>
                  </span>
                </button>
              </div>
            </div>

            <!-- CONTENT -->
            {{-- <div class="bs-stepper-content"> --}}
              <div id="kyc-docs-{{$document['id']}}" class="content m-4">
                @if ($document->id == request()->document_id)
                  @includeWhen(!isset($modelInstance),'admin.pages.contracts.pending-documents.fields')
                  @includeWhen(isset($modelInstance), 'admin.pages.contracts.uploaded-docs.edit')
                @endif
              </div>
            {{-- </div> --}}
          @empty
            <div class="alert alert-info">
              <h4 class="alert-heading">All Done</h4>
              <p class="mb-0">There are no pending documents for now.</p>
            </div>
          @endforelse
        </div>
      @else
        <div class="alert alert-info">
          <h4 class="alert-heading">All Done</h4>
          <p class="mb-0">There are no pending documents for now.</p>
        </div>
      @endif
    </div>
  </div>
</div>

@endsection

