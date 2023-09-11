@extends('admin/layouts/layoutMaster')

@section('title', $page.' Milestones')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-projects-milestones.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-profile.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="https://rawgit.com/bevacqua/dragula/master/dist/dragula.js"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/custom/admin-project-milestones.js')}}"></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  window.active_project = '{{$contract->project_id ?? "project"}}';
  window.active_contract = '{{$contract->id}}';
  window.active_phase = '{{$phase->id ?? "phase"}}';
</script>
<script src="https://cdn.jsdelivr.net/npm/rrule@2.7.2/dist/es5/rrule.min.js"></script>

<script>
$(document).ready(function(){

console.log(rrule.rrulestr('DTSTART:20120201T023000Z\nRRULE:FREQ=MONTHLY;COUNT=5'));

});
</script>
@endsection

@section('content')
@includeWhen($page == 'Project', 'admin.pages.projects.navbar', ['tab' => 'milestones'])
@includeWhen($page == 'Contract', 'admin.pages.contracts.header', ['tab' => 'milestones'])
<div class="app-email mt-3 card">
  <div class="row g-0">
    <!-- Task Sidebar -->
    <div class="col app-email-sidebar border-end flex-grow-0" id="app-email-sidebar">
      <div class="btn-compost-wrapper d-grid">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="Add Milestone" data-href="{{route('admin.projects.contracts.phases.milestones.create', ['project' => $project, $contract, $phase])}}">Add Milestone</button>
      </div>
      <div class="email-filters py-2">
        <small class="fw-normal text-uppercase text-muted m-4">Milestone Status</small>
        <ul class="email-filter-folders list-unstyled mb-4">
          <li class="active d-flex justify-content-between" data-target="inbox">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">All</span>
            </a>
            <div class="badge bg-label-success rounded-pill badge-center">{{$phase->milestones->count()}}</div>
          </li>
          @forelse ($milestone_statuses as $status)
            <li class="d-flex justify-content-between" data-target="{{slug($status)}}">
              <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                <span class="align-middle ms-2">{{$status}}</span>
              </a>
              <div class="badge bg-label-{{$colors[$status]}} rounded-pill badge-center">{{$phase->milestones->where('status', $status)->count()}}</div>
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
                  <input type="text" class="form-control email-search-input border-0" placeholder="Search Milestone">
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-0 mb-md-2">
              <i class="ti ti-rotate-clockwise rotate-180 scaleX-n1-rtl cursor-pointer email-refresh me-2 mt-1" onclick="refreshMilestoneList();"></i>
            </div>
          </div>
        </div>
        <hr class="container-m-nx m-0">
        <!-- Task List: Items -->
        <div class="email-list pt-0">
          <ul class="list-unstyled m-0 todo-task-list tasks-list tasks">
            @include('admin.pages.contracts.milestones.milestone-list')
          </ul>
        </div>
      </div>
      <div class="app-overlay"></div>
    </div>
    <!-- /Task List -->
  </div>
</div>
@endsection
