@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'React App')

@section('content')
<h4 class="fw-semibold mb-4">{{__('React App')}}</h4>

<div class="mt-3  col-12">
  <div id="react-notes-app"></div>
</div>

@endsection
@push('scripts')
    <script src={{asset('js/react-notes.js')}}></script>
@endpush
