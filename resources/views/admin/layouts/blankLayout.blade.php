@isset($pageConfigs)
{!! Helper::updateAdminPageConfig($pageConfigs) !!}
@endisset
@php
$configData = Helper::appAdminClasses();

/* Display elements */
$customizerHidden = ($customizerHidden ?? '');

@endphp

@extends('admin.layouts/commonMaster' )

@section('layoutContent')

<!-- Content -->
@yield('content')
<!--/ Content -->

@endsection
