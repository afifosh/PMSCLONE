@extends('admin/layouts/layoutMaster')

@section('title', $page.' Phases')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<style>
  .expanded-row{
    background-color: rgb(202, 202, 209) !important;
  }
</style>
@livewireStyles
<x-comments::styles />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-projects-phases.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  window.active_project = '{{$contract->project_id ?? "project"}}';
  window.active_contract = '{{$contract->id}}';
  window.active_stage = '{{$stage->id ?? "stage"}}';
</script>
<script src={{asset('assets/js/custom/admin-contract-phase-create.js')}}></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/rrule@2.7.2/dist/es5/rrule.min.js"></script> --}}

<script>
$(document).ready(function(){
// console.log(rrule.rrulestr('DTSTART:20120201T023000Z\nRRULE:FREQ=MONTHLY;COUNT=5'));

});
function initSortable() {
  var sortable = Sortable.create($('#phases-table tbody')[0], {
    handle: '.bi-drag',
    group: 'shared',
    animation: 150,
    dataIdAttr: 'data-id',
    onSort: function (/**Event*/evt) {
      $.ajax({
        url: route('admin.projects.contracts.sort-phases', { project: window.active_project, contract: window.active_contract }),
        type: "PUT",
        data: {
          phases: sortable.toArray(),
        },
        success: function(res){
        }
      });
    },

  });
}
</script>
@endsection

@section('content')
{{-- @includeWhen($page == 'Project', 'admin.pages.projects.navbar', ['tab' => 'phases']) --}}
@includeWhen($page == 'Contract', 'admin.pages.contracts.header', ['tab' => 'phases'])
@includeWhen($page == 'Contract All', 'admin.pages.contracts.header', ['tab' => 'all-phases'])
<div class="card mt-3">
  <div class="card-body">
    {{$dataTable->table()}}
  </div>
</div>
@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    <script>
      window.oURL = window.location.href;

      function reloadDataTables()
      {
        $('#phases-table').DataTable().ajax.reload(null, false);
      }
    </script>
    @livewireScripts
    <x-comments::scripts />
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

