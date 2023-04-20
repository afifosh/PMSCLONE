@extends('admin/layouts/layoutMaster')

@section('title', 'Company Profile')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-chat.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('assets/js/custom/admin-company-profile-page.js')}}"></script>
    {{-- <script src="{{asset('assets/js/app-chat.js')}}"></script> --}}
@endsection

@section('content')
    <div class="d-md-flex justify-content-between flex-row-reverse">
        <div class="card ms-md-2 col-md-3">
            <div class="card-body">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="{{ $company->verified_at ? asset('assets/img/company/verified.png') : asset('assets/img/company/p-verified.png') }}" alt="Avatar" height="150"
                        class="rounded-circle">
                </div>
                <h5 class="card-title mb-0 mt-1 text-center">Verification Status</h5>
                <p class="card-text text-center">Add Info In all the five forms</p>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: {{($overAllStatus*100)/5}}%" aria-valuenow="{{($overAllStatus*100)/5}}" aria-valuemin="0"
                        aria-valuemax="100"></div>
                </div>
                <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'details']) }}" class="border fw-bold mt-2 d-flex justify-content-between p-2 {{request()->tab == 'details' ? 'bg-label-primary' : ''}}">
                    <span> 1. Company Details </span>
                    <span class="text-{{ getCompanyStatusColor($detailsStatus) }}"><i class="{{getCompanyStatusIcon($detailsStatus)}} fa-lg"></i></span>
                </a>
                <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'contact-persons']) }}" class="border fw-bold mt-2 d-flex justify-content-between p-2 {{request()->tab == 'contact-persons' ? 'bg-label-primary' : ''}}">
                    <span> 2. Contact Persons </span>
                    <span class="text-{{ getCompanyStatusColor($contactsStatus) }}"><i class="{{getCompanyStatusIcon($contactsStatus)}} fa-lg"></i></span>
                </a>
                <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'addresses']) }}" class="border fw-bold mt-2 d-flex justify-content-between p-2 {{request()->tab == 'addresses' ? 'bg-label-primary' : ''}}">
                    <span> 3. Company Addresses </span>
                    <span class="text-{{ getCompanyStatusColor($addressesStatus) }}"><i class="{{getCompanyStatusIcon($addressesStatus)}} fa-lg"></i></span>
                </a>
                <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'documents']) }}" class="border fw-bold mt-2 d-flex justify-content-between p-2 {{request()->tab == 'documents' ? 'bg-label-primary' : ''}}">
                    <span> 4. Verification Documents </span>
                    <span class="text-{{ getCompanyStatusColor($kycDocStatus) }}"><i class="{{getCompanyStatusIcon($kycDocStatus)}} fa-lg"></i></span>
                </a>
                <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'bank-accounts']) }}" class="border fw-bold mt-2 d-flex justify-content-between p-2 {{request()->tab == 'bank-accounts' ? 'bg-label-primary' : ''}}">
                    <span> 5. Bank Accounts </span>
                    <span class="text-{{ getCompanyStatusColor($accountsStatus) }}"><i class="{{getCompanyStatusIcon($accountsStatus)}} fa-lg"></i></span>
                </a>
            </div>
        </div>
        <div class="w-100">
          @include('admin.pages.company.approval-request.vertical.tabs.'.request()->tab)
        </div>
    </div>
@endsection
