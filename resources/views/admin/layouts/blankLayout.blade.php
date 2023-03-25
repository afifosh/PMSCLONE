@isset($pageConfigs)
{!! Helper::updateAdminPageConfig($pageConfigs) !!}
@endisset
@php
$configData = Helper::appAdminClasses();

/* Display elements */
$customizerHidden = ($customizerHidden ?? '');
$containerNav = ($containerNav ?? 'container-xxl');
$isFooter = ($isFooter ?? true);
@endphp

@extends('admin.layouts/commonMaster' )

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
