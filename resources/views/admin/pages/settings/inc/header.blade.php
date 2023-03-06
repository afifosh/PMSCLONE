@php
    $configData = Helper::appClasses();
@endphp

@section('title', 'Settings')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{asset('assets/css/app-settings.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('assets/js/custom/app-settings.js')}}"></script>
@endsection