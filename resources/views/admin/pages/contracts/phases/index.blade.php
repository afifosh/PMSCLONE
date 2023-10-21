@extends('admin/layouts/layoutMaster')

@section('title', $page.' Phases')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
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
@endsection

@section('page-script')
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  window.active_project = '{{$contract->project_id ?? "project"}}';
  window.active_contract = '{{$contract->id}}';
  window.active_stage = '{{$stage->id ?? "stage"}}';
</script>
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

function createInvoices(){
  var phases = [];
  $('.phase-check:checked').each(function(){
    phases.push($(this).val());
  });
  if(phases.length == 0){
    return;
  }
  $.ajax({
    url: route('admin.contracts.bulk-invoices.store', { contract: window.active_contract }),
    type: "POST",
    data: {
      phases: phases,
    },
    success: function(res){
      $('.phase-check-all').prop('checked', false).trigger('change');
      $('.phase-check').prop('checked', false).trigger('change');
      toggleCheckboxes();
    }
  });
}

$(document).on('click', '.phase-check-all', function(){
  if($(this).is(':checked')){
    $('.phase-check').prop('checked', true).trigger('change');
  }else{
    $('.phase-check').prop('checked', false).trigger('change');
  }
})

$(document).on('click change', '.phase-check', function(){
  if($('.phase-check:checked').length == $('.phase-check').length){
    $('.phase-check-all').prop('checked', true);
  }else{
    $('.phase-check-all').prop('checked', false);
  }

  // if atleast one is checked, show create-inv-btn
  if($('.phase-check:checked').length > 0){
    $('.select-phases-btn').addClass('d-none');
    $('.create-inv-btn').removeClass('d-none');
  }else{
    $('.create-inv-btn').addClass('d-none');
    $('.select-phases-btn').removeClass('d-none');
  }
})

function toggleCheckboxes(){
  $('#phases-table').DataTable().column(1).visible(!$('#phases-table').DataTable().column(1).visible());
  $('#phases-table').DataTable().ajax.reload();
}
</script>
@endsection

@section('content')
@includeWhen($page == 'Project', 'admin.pages.projects.navbar', ['tab' => 'phases'])
@includeWhen($page == 'Contract', 'admin.pages.contracts.header', ['tab' => 'phases'])
{{-- <div class="app-email mt-3 card">
  <div class="row g-0">
    <!-- Task Sidebar -->
    <div class="col app-email-sidebar border-end flex-grow-0" id="app-email-sidebar">
      <div class="btn-compost-wrapper d-grid">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="Add Phase" data-href="{{}}">Add Phase</button>
      </div>
      <div class="email-filters py-2">
        <small class="fw-normal text-uppercase text-muted m-4">Phase Status</small>
        <ul class="email-filter-folders list-unstyled mb-4">
          <li class="active d-flex justify-content-between" data-target="inbox">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">All</span>
            </a>
            <div class="badge bg-label-success rounded-pill badge-center">{{$stage->phases->count()}}</div>
          </li>
          @forelse ($phase_statuses as $status)
            <li class="d-flex justify-content-between" data-target="{{slug($status)}}">
              <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                <span class="align-middle ms-2">{{$status}}</span>
              </a>
              <div class="badge bg-label-{{$colors[$status]}} rounded-pill badge-center">{{$stage->phases->where('status', $status)->count()}}</div>
            </li>
          @empty
          @endforelse
        </ul>
      </div>
    </div>
    <!--/ Task Sidebar -->

    <!-- Task List -->
    <div class="col app-emails-list">
      <div class="shadow-none border-0">
        <div class="emails-list-header p-3 py-lg-3 py-2">
          <!-- Task List: Search -->
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center w-100">
              <i class="ti ti-menu-2 ti-sm cursor-pointer d-block d-lg-none me-3"></i>
              <div class="mb-0 mb-lg-2 w-100">
                <div class="input-group input-group-merge shadow-none">
                  <span class="input-group-text border-0 ps-0" id="email-search">
                    <i class="ti ti-search"></i>
                  </span>
                  <input type="text" class="form-control email-search-input border-0" placeholder="Search Phase">
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-0 mb-md-2">
              <i class="ti ti-rotate-clockwise rotate-180 scaleX-n1-rtl cursor-pointer email-refresh me-2 mt-1" onclick="refreshPhaseList();"></i>
            </div>
          </div>
        </div>
        <hr class="container-m-nx m-0">
        <!-- Task List: Items -->
        <div class="email-list pt-0">
          <ul class="list-unstyled m-0 todo-task-list tasks-list tasks">
            @include('admin.pages.contracts.phases.phase-list')
          </ul>
        </div>
      </div>
      <div class="app-overlay"></div>
    </div>
    <!-- /Task List -->
  </div>
</div> --}}
<div class="card mt-3">
  <div class="card-body">
    {{$dataTable->table()}}
  </div>
</div>
@endsection
@push('scripts')
    {{$dataTable->scripts()}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

