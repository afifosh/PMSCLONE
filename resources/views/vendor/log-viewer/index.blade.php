@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Logs')

@section('vendor-style')
    {{-- <meta http-equiv="X-UA-Compatible" content="ie=edge"> --}}
    {{-- <link rel="shortcut icon" href="{{ asset(mix('img/log-viewer-32.png', 'vendor/log-viewer')) }}"> --}}
    <link href="{{ asset(mix('app.css', 'vendor/log-viewer')) }}" rel="stylesheet" onerror="alert('app.css failed to load. Please refresh the page, re-publish Log Viewer assets, or fix routing for vendor assets.')">

@endsection
@section('content')
{{-- <body class="h-full px-3 lg:px-5 bg-gray-100 dark:bg-gray-900"> --}}
<div id="log-viewer" class="flex h-full max-h-screen max-w-full">
    <router-view></router-view>
</div>
@endsection

@push('scripts')
<!-- Global LogViewer Object -->
  <script>
      window.LogViewer = @json($logViewerScriptVariables);

      // Add additional headers for LogViewer requests like so:
      // window.LogViewer.headers['Authorization'] = 'Bearer xxxxxxx';
  </script>
  <script src="{{ asset(mix('app.js', 'vendor/log-viewer')) }}" onerror="alert('app.js failed to load. Please refresh the page, re-publish Log Viewer assets, or fix routing for vendor assets.')"></script>
@endpush
