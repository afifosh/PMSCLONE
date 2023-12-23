@extends('admin.layouts/layoutMaster')
@section('title', __('Form Templates'))
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Form Templates') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('admin.dashboard'),__('Dashboard'),['']) !!}</li>
            <li class="breadcrumb-item">{{ __('Form Templates') }}</li>
        </ul>
    </div>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    {{ $dataTable->table(['width' => '100%']) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('style')
@include('admin.pages.applications.form.theme-essentials')
<link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/datatables/buttons.bootstrap.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('vendor/js/custom.js') }}"></script>
    <script src="{{asset('vendor/datatables/datatables.min.js')}}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {{ $dataTable->scripts() }}
@endpush
