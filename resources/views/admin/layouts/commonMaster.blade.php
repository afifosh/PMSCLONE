<!DOCTYPE html>

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}" class="{{ $configData['style'] }}-style {{ $navbarFixed ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}" dir="{{ $configData['textDirection'] }}" data-theme="{{ $configData['theme'] }}" data-assets-path="{{ asset('/assets') . '/' }}" data-base-url="{{url('/')}}" data-framework="laravel" data-template="{{ $configData['layout'] . '-menu-' . $configData['theme'] . '-' . $configData['style'] }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>@yield('title') |
    {{ config('variables.templateName') ? config('variables.templateName') : 'TemplateName' }} -
    {{ config('variables.templateSuffix') ? config('variables.templateSuffix') : 'TemplateSuffix' }}</title>
  <meta name="description" content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta name="keywords" content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Canonical SEO -->
  <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

  <!-- Include Styles -->
  @include('admin/layouts/sections/styles')

  <!-- Include Scripts for customizer, helper, analytics, config -->
  @include('admin/layouts/sections/scriptsIncludes')
</head>

<body style="overflow-x: hidden;"  class="{{ !empty($body_class) ? $body_class : '' }}">
  <style>
    div.dataTables_scrollBody.dropdown-visible {
    overflow: visible !important;
  }
  </style>

  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->


  <!-- Global Model -->
  @include('admin._partials._modals.global-modal')
  @include('admin._partials._offcanvas.offcanvas-global')
  <!-- Global Model -->

  <!-- Include Scripts -->
  @include('admin/layouts/sections/scripts')

  @stack('scripts')

  <script>
    $(document).ready(function () {
      $('.table.dataTable').on('show.bs.dropdown', function () {
        $('.dataTables_scrollBody').addClass('dropdown-visible');
      })
      .on('hide.bs.dropdown', function () {
        $('.dataTables_scrollBody').removeClass('dropdown-visible');
      });
    });
  </script>
</body>

</html>
