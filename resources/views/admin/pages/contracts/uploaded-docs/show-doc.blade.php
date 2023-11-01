@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Uploaded Documents')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
@livewireStyles
<x-comments::styles />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection

@section('content')
@includeWhen(isset($contract) ,'admin.pages.contracts.header', ['tab' => 'uploaded-documents'])
@includeWhen(isset($invoice) ,'admin.pages.invoices.header-top', ['tab' => 'uploaded-documents'])
  <div class="mt-3  col-12">
    <div class="col-xl-12">
      <div class="d-flex justify-content-between">
         <h6 class="text-muted">{{$document->title}}</h6>
         <div class="col-2">
          <select class="form-select select2 change_version">
            @forelse ($doc->versions as $version)
              <option value="{{$version->id}}" @if($version->id == $doc->id) selected @endif>Version {{count($doc->versions) - $loop->index}}</option>
            @empty
            @endforelse
          </select>
         </div>

      </div>
      <div class="nav-align-top nav-tabs-shadow mb-4">
        <ul class="nav nav-tabs nav-fill" role="tablist">
          <li class="nav-item">
            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#nav-doc-details" aria-controls="nav-doc-details" aria-selected="true"><i class="tf-icons ti ti-home ti-xs me-1"></i> Details </button>
          </li>
          <li class="nav-item {{$doc->requestedDoc->signatures_required < 1 ? 'd-none': ''}}">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-doc-signs" aria-controls="nav-doc-signs" aria-selected="false" tabindex="-1"><i class="tf-icons ti ti-user ti-xs me-1"></i> Signatures </button>
          </li>
          <li class="nav-item {{$doc->requestedDoc->stamps_required < 1 ? 'd-none': ''}}">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-doc-stamps" aria-controls="nav-doc-stamps" aria-selected="false" tabindex="-1"><i class="tf-icons ti ti-user ti-xs me-1"></i> Stamps </button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-doc-comments" aria-controls="nav-doc-comments" aria-selected="false" tabindex="-1"><i class="tf-icons ti ti-message-dots ti-xs me-1"></i> Comments</button>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade active show" id="nav-doc-details" role="tabpanel">
            @include('admin.pages.contracts.uploaded-docs.show', ['hide_close' => true])
          </div>
          <div class="tab-pane fade" id="nav-doc-signs" role="tabpanel">
            {!! $signaturesTable->html()->table() !!}
          </div>
          <div class="tab-pane fade" id="nav-doc-stamps" role="tabpanel">
            {!! $stampsTable->html()->table() !!}
          </div>
          <div class="tab-pane fade" id="nav-doc-comments" role="tabpanel">
            <livewire:comments :model="$doc" />
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('scripts')
  {!! $signaturesTable->html()->scripts() !!}
  {!! $stampsTable->html()->scripts() !!}
  @livewireScripts
  <x-comments::scripts />
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
    $(document).ready(function () {
      $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {
        $('#stamps-table').DataTable().columns.adjust().responsive.recalc();
        $('#signatures-table').DataTable().columns.adjust().responsive.recalc();
      } );

    });

    $('.change_version').on('change', function(){
      @if(isset($contract))
        window.location.href = route('admin.contracts.uploaded-documents.show', {contract: {{$contract->id}}, uploaded_document: $(this).val()} );
      @elseif(isset($invoice))
       window.location.href = route('admin.invoices.uploaded-documents.show', {invoice: {{$invoice->id}}, uploaded_document: $(this).val()} );
      @endif
    })
  </script>
@endpush
