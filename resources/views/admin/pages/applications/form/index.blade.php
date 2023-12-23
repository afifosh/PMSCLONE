@extends('admin.layouts/layoutMaster')

@section('title', 'Forms')
{{-- @section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/datatables/buttons.bootstrap.min.css') }}">
@endsection --}}

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
@endsection
@section('breadcrumb')
    <div class="col-md-12">
        <div class="page-header-title">
            <h4 class="m-b-10">{{ __('Forms') }}</h4>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item">{!! Html::link(route('admin.dashboard'), __('Dashboard'), []) !!}</li>
            <li class="breadcrumb-item active"> {{ __('Forms') }} </li>
        </ul>
        <div class="float-end">
            <div class="d-flex align-items-center">
                <a href="#{{-- route('grid.form.view', 'view') --}}" data-bs-toggle="tooltip" title="{{ __('Grid View') }}"
                    class="btn btn-sm btn-primary" data-bs-placement="bottom">
                    <i class="ti ti-layout-grid"></i>
                </a>
            </div>
        </div>
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
    {{-- @include('layouts.includes.datatable-js') --}}
    <script src="{{asset('vendor/datatables/datatables.min.js')}}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('vendor/js/custom.js') }}"></script>
    {{ $dataTable->scripts() }}
    <script>
        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).attr('data-url')).select();
            document.execCommand("copy");
            $temp.remove();
            show_toastr('Great!', '{{ __('Copy Link Successfully.') }}', 'success',
                '{{ asset('assets/images/notification/ok-48.png') }}', 4000);
        }
        $(function() {
            $('body').on('click', '#share-qr-code', function() {
                var action = $(this).data('share');
                var modal = $('#globalModal');
                $.get(action, function(response) {
                    modal.find('.modal-title').html('{{ __('QR Code') }}');
                    modal.find('.modal-body').html(response.html);
                    feather.replace();
                    modal.modal('show');
                })
            });
        });
    </script>
@endpush
