@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
$configData = Helper::appClasses();

/* Display elements */
$customizerHidden = ($customizerHidden ?? '');
$containerNav = ($containerNav ?? 'container-xxl');
$isFooter = ($isFooter ?? true);
@endphp

@extends('layouts/commonMaster' )

@section('layoutContent')

<!-- Content -->
@yield('content')
<!--/ Content -->

<!-- Footer -->
@if ($isFooter)
@include('admin/layouts/sections/footer/footer')
@endif
<!-- / Footer -->
@endsection
