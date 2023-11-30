@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Access List')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<style>
  .treeselect .treeselect-list {
    position: relative !important;
  }
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script src="https://cdn.jsdelivr.net/npm/treeselectjs@0.10.0/dist/treeselectjs.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/treeselectjs@0.10.0/dist/treeselectjs.css" />
<script>
  function initACLCreateTreeSelect()
  {
    const options = [
      {
        name: 'England',
        value: 1,
        children: [
          {
            name: 'London',
            value: 2,
            children: [
              {
                name: 'Chelsea',
                value: 3,
                children: []
              },
              {
                name: 'West End',
                value: 4,
                children: []
              }
            ]
          },
          {
            name: 'Brighton',
            value: 5,
            children: []
          }
        ]
      },
      {
        name: 'France',
        value: 6,
        children: [
          {
            name: 'Paris',
            value: 7,
            children: []
          },
          {
            name: 'Lyon',
            value: 8,
            children: []
          }
        ]
      }
    ]

    const domElement = document.querySelector('.acl-create-treeselect')
    const treeselect = new Treeselect({
      parentHtmlContainer: domElement,
      value: [4, 7, 8],
      options: options,
      isIndependentNodes: true,
      showCount: true,
      openLevel: 150
    })

    treeselect.srcElement.addEventListener('input', (e) => {
      console.log('Selected value:', e.detail)
    })
  }
</script>
@endsection

@section('content')
<h4 class="fw-semibold mb-4">{{__('Access List')}}</h4>

<div class="mt-3  col-12">
  <div class="card">
    <div class="card-body">
      {{$dataTable->table()}}
    </div>
  </div>
</div>

@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
@endpush
