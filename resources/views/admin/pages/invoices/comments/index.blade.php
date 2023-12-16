@extends('admin/layouts/layoutMaster')

@section('title', 'Invoice Comments')

@section('vendor-style')
<style>
  .expanded-row{
    background-color: rgb(202, 202, 209) !important;
  }
</style>
@livewireStyles
<x-comments::styles />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-invoice.css')}}" />
@endsection

@section('content')
@includeWhen(isset($invoice) ,'admin.pages.invoices.header-top', ['tab' => request()->tab ?? 'comments'])
  <div class="col-12 mb-lg-0 mb-4">
    <div class="card invoice-preview-card">
      <div class="card-body">
        @include('admin._partials.sections.x-comments', ['model' => $invoice])
      </div>
    </div>
  </div>
@endsection
@push('scripts')
  @livewireScripts
  <x-comments::scripts />
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
