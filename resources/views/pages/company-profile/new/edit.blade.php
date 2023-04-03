@extends('layouts/layoutMaster')

@section('title', 'Company Profile')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/custom/company-profile-page.js') }}"></script>
    <script src="{{asset('assets/js/custom/company-profile-page.js')}}"></script>
@endsection

@section('content')
    <div class="d-md-flex justify-content-between flex-row-reverse">
        <div class="card ms-md-2 col-md-3 align-self-start mb-2">
            <div class="card-body">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/avatars/7.png"alt="Avatar"
                        class="rounded-circle">
                </div>
                <h5 class="card-title mb-0 mt-1 text-center">Verification Status</h5>
                <p class="card-text text-center">Add Info In all the five forms</p>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0"
                        aria-valuemax="100"></div>
                </div>
                <div class="border fw-bold mt-2 d-flex justify-content-between p-2">
                    <span> 1. Company Details </span>
                    <span class="text-success"><i class="fa-regular fa-circle-check fa-lg"></i></span>
                </div>
                <div class="border fw-bold mt-2 d-flex justify-content-between p-2">
                    <span> 2. Contact Persons </span>
                    <span class="text-success"><i class="fa-regular fa-circle-check fa-lg"></i></span>
                </div>
                <div class="border fw-bold mt-2 d-flex justify-content-between p-2">
                    <span> 3. Company Addresses </span>
                    <span class="text-danger"><i class="fa-regular fa-circle-xmark fa-lg"></i></span>
                </div>
                <div class="border fw-bold mt-2 d-flex justify-content-between p-2">
                    <span> 4. Verification Documents </span>
                    <span class="text-success"><i class="fa-regular fa-circle-check fa-lg"></i></span>
                </div>
                <div class="border fw-bold mt-2 d-flex justify-content-between p-2">
                    <span> 5. Bank Accounts </span>
                    <span class="text-danger"><i class="fa-regular fa-circle-xmark fa-lg"></i></span>
                </div>
            </div>
        </div>
        <div class="card w-100">
            <div class="card-body">
                <h4 class="card-title">Company Profile</h4>
                <p class="card-text">You have to provide only these things to get verified</p>
                <div class="card mb-2">
                    <div class="card-body d-flex">
                        <div class="avatar me-2">
                            <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/avatars/7.png"alt="Avatar"
                                class="rounded-circle">
                        </div>
                        <div>
                            <h6 class="card-title my-0"> 1) Profile Details</h6>
                            <p class="card-text">Some General Details about your company</p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{route('company.profile.detailedContent')}}">Change</a>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body d-flex">
                        <div class="avatar me-2">
                            <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/avatars/7.png"alt="Avatar"
                                class="rounded-circle">
                        </div>
                        <div>
                            <h6 class="card-title my-0"> 2) Contact Persons</h6>
                            <p class="card-text">Persons to contact in your company</p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{route('company.profile.detailedContent')}}">Change</a>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body d-flex">
                        <div class="avatar me-2">
                            <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/avatars/7.png"alt="Avatar"
                                class="rounded-circle">
                        </div>
                        <div>
                            <h6 class="card-title my-0"> 3) Company Addresses</h6>
                            <p class="card-text">Manage addresses of your company</p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{route('company.profile.detailedContent')}}">Change</a>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body d-flex">
                        <div class="avatar me-2">
                            <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/avatars/7.png"alt="Avatar"
                                class="rounded-circle">
                        </div>
                        <div>
                            <h6 class="card-title my-0"> 4) Verification Documents</h6>
                            <p class="card-text">These are the required documents to verify the company</p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{route('company.profile.detailedContent')}}">Change</a>
                        </div>
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body d-flex">
                        <div class="avatar me-2">
                            <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/avatars/7.png"alt="Avatar"
                                class="rounded-circle">
                        </div>
                        <div>
                            <h6 class="card-title my-0"> 5) Bank Accounts</h6>
                            <p class="card-text">Company Bank Accounts</p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{route('company.profile.detailedContent')}}">Change</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
