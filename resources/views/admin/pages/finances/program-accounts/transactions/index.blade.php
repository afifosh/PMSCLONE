@php
    $configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Program Transactions')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
    <script src={{ asset('assets/js/custom/select2.js') }}></script>
    <script src={{ asset('assets/js/custom/flatpickr.js') }}></script>
@endsection

@section('content')
    <div class="card col-7 border-0 b-shadow-4 mb-4">
        <div class="card-body ">
            <div class="d-flex justify-content-between">
                <div class="d-flex">
                    <span class="mt-1 me-2"><i class="fa-solid fa-xl fa-landmark"></i></span>
                    <div class="ml-2">
                        <h3 class="heading-h3">{{$programAccount->name}}</h3>
                        <p class="f-12 font-weight-normal text-dark-grey mb-0">
                           <span class="text-primary font-weight-semibold">{{$programAccount->printableAccountNumber()}}</span>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="f-12 text-dark-grey">Account Balance</div>
                    <h2 class="heading-h2 text-primary mt-2">{{$programAccount->printableBalance()}}</h2>
                </div>
            </div>
            <div class="card-footer bg-white border-top-grey px-0 mt-3">
                <div class="d-flex justify-content-between mt-3">
                    <button
                        data-toggle="ajax-modal" data-title="Deposite" data-href="{{route('admin.finances.program-accounts.transactions.create', [$programAccount->id, 'type' => 'deposit'])}}"
                        class="btn btn-outline-secondary rounded"><i class="fa fa-plus-circle me-2"></i>
                        Deposit
                    </button>
                    <button
                        data-toggle="ajax-modal" data-title="Transfer To Other Account" data-href="{{route('admin.finances.program-accounts.transactions.create', [$programAccount->id, 'type' => 'transfer'])}}"
                        class="btn btn-outline-secondary rounded"><i class="fa fa-exchange-alt me-2"></i>
                        Bank Account Transfer
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3  col-12">
        <div class="card">
            <div class="card-body">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    {{ $dataTable->scripts() }}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
@endpush
