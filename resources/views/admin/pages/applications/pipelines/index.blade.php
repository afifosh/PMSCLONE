@php
$configData = Helper::appClasses();
@endphp

@extends('admin.layouts/layoutMaster')

@section('title', 'Application Pipelines')

@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
  <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
@endsection

@section('page-script')
  <script src={{asset('assets/js/custom/select2.js')}}></script>
  <script src={{asset('assets/js/custom/flatpickr.js')}}></script>
  <script src="{{ asset('assets/js/scripts/repeater.js') }}"></script>
  <script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
  <script>
    function initRepeater() {
      const repeater = $('.repeater');

      initSortable();
      repeater.repeater({
        defaultValues: {
            'name': ''
        },
        show: function () {
            $(this).slideDown();
            initSortable();
        },
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        },
        ready: function (setIndexes) {
          // sortable.on('sortstop', setIndexes);
            $(document).on('click', '.btn', function () {
                setIndexes();
            });
        },
        isFirstItemUndeletable: true
      });
    }

    function initSortable() {
      const sortable = Sortable.create($('#stages-container')[0], {
        handle: '.bi-drag',
        group: 'shared',
        animation: 150,
        dataIdAttr: 'data-id',
        onSort: function (/**Event*/evt) {
          console.log(sortable.toArray());
          // $.ajax({
          //   url: route('admin.projects.contracts.sort-phases', { project: window.active_project, contract: window.active_contract }),
          //   type: "PUT",
          //   data: {
          //     phases: sortable.toArray(),
          //   },
          //   success: function(res){
          //   }
          // });
        },
        update: function(event, ui) {
          repeater.repeater( 'setIndexes' );
        }
      });
    }
    // $(document).ready(function() {
    //   const variantRepeat = $('.variant-repeater');
    //   const sortable = $(".sortable").Sortable();
    //   variantRepeat.repeater({
    //     show: function () {
    //       $(this).slideDown();
    //       // Feather Icons
    //       if (feather) {
    //           feather.replace({ width: 14, height: 14 });
    //         }
    //     },
    //     hide: function (deleteElement) {
    //       if (confirm('Are you sure you want to delete this element?')) {
    //         $(this).slideUp(deleteElement);
    //       }
    //     },
    //     ready: function(setIndexes) {
    //       sortable.on("sortstop", setIndexes); // sortstop instead of sortchange
    //     }
    //   });
    // });
  </script>
@endsection

@section('content')
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
@endpush
