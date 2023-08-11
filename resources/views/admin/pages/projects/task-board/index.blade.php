@extends('admin/layouts/layoutMaster')

@section('title', 'Task Templates')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-email.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/custom/admin-project-taskboard.js')}}"></script>
@endsection

@section('content')
@include('admin.pages.projects.navbar', ['tab' => 'task-board'])
<div class="app-email mt-1 card">
  <div class="row g-0">
    <!-- Email Sidebar -->
    <div class="col app-email-sidebar border-end flex-grow-0" id="app-email-sidebar">
      <div class="btn-compost-wrapper d-grid">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="Add Task" data-href="{{route('admin.projects.tasks.create', [$project])}}">Add Task</button>
      </div>
      <div class="email-filters py-2">
        <small class="fw-normal text-uppercase text-muted m-4">Tasks Status</small>
        <ul class="email-filter-folders list-unstyled mb-4">
          <li class="active d-flex justify-content-between" data-target="inbox">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              {{-- <span class="badge badge-dot bg-warning"></span> --}}
              <span class="align-middle ms-2">All</span>
            </a>
          </li>
          <li class="d-flex justify-content-between" data-target="mine">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">My Task</span>
            </a>
          </li>
          @forelse ($task_statuses as $status)
            <li class="d-flex justify-content-between" data-target="{{slug($status)}}">
              <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                {{-- <span class="badge badge-dot bg-success"></span> --}}
                <span class="align-middle ms-2">{{$status}}</span>
              </a>
            </li>
          @empty
          @endforelse
        </ul>
        @php
            $colors = ['Low' => 'success', 'Medium' => 'warning', 'High' => 'danger', 'Urgent' => 'danger'];
        @endphp
        <small class="fw-normal text-uppercase text-muted m-4">Priority</small>
        <ul class="email-filter-folders list-unstyled mb-4">
          <li class="d-flex justify-content-between" data-target="inbox">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="badge badge-dot bg-warning"></span>
              <span class="align-middle ms-2">All</span>
            </a>
          </li>
          @forelse ($priorities as $priority)
            <li class="d-flex justify-content-between" data-target="{{$priority}}">
              <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                <span class="badge badge-dot bg-{{$colors[$priority]}}"></span>
                <span class="align-middle ms-2">{{$priority}}</span>
              </a>
            </li>
          @empty
          @endforelse
        </ul>
      </div>
    </div>
    <!--/ Email Sidebar -->

    <!-- Emails List -->
    <div class="col app-emails-list">
      <div class="shadow-none border-0">
        <div class="emails-list-header p-3 py-lg-3 py-2">
          <!-- Email List: Search -->
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center w-100">
              <i class="ti ti-menu-2 ti-sm cursor-pointer d-block d-lg-none me-3"></i>
              <div class="mb-0 mb-lg-2 w-100">
                <div class="input-group input-group-merge shadow-none">
                  <span class="input-group-text border-0 ps-0" id="email-search">
                    <i class="ti ti-search"></i>
                  </span>
                  <input type="text" class="form-control email-search-input border-0" placeholder="Search Task">
                </div>
              </div>
            </div>
            {{-- <div class="d-flex align-items-center mb-0 mb-md-2">
              <i class="ti ti-rotate-clockwise rotate-180 scaleX-n1-rtl cursor-pointer email-refresh me-2 mt-1"></i>
            </div> --}}
          </div>
        </div>
        <hr class="container-m-nx m-0">
        <!-- Email List: Items -->
        <div class="email-list pt-0">
          <ul class="list-unstyled m-0 tasks-list">
            @forelse ($project->tasks as $task)
              <li class="email-list-item email-marked-read fw-bold" data-mine="{{$task->assignees->where('id', auth()->id())->count() > 0}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
                <div class="d-flex align-items-center">
                  {{-- <div class="form-check mb-0">
                    <input class="email-list-item-input form-check-input" type="checkbox" id="email-1">
                    <label class="form-check-label" for="email-1"></label>
                  </div> --}}
                  {{-- <i class="email-list-item-bookmark ti ti-star ti-xs d-sm-inline-block d-none cursor-pointer ms-2 me-3"></i> --}}
                  {{-- <img src="{{ asset('assets/img/avatars/1.png') }}" alt="user-avatar" class="d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" /> --}}
                  <div class="email-list-item-content ms-2 ms-sm-0 me-2">
                    {{-- <span class="h6 email-list-item-username me-2">Chandler Bing</span> --}}
                    <span class="email-list-item-subject d-xl-inline-block d-block">{{$task->subject}}</span>
                  </div>
                  <div class="email-list-item-meta ms-auto d-flex align-items-center">
                    {{-- <small class="email-list-item-time text-muted">{{$task->created_at->diffForHumans()}}</small> --}}
                    <span class="badge bg-label-{{$colors[$task->priority]}} me-2">{{$task->priority}}</span>
                    @isset($task->assignees[0])
                    <img src="{{ $task->assignees[0]->avatar }}" alt="user-avatar" class="d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
                    @endisset
                    <ul class="list-inline email-list-item-actions text-nowrap">
                      {{-- <li class="list-inline-item email-read"> <i class='ti ti-mail-opened'></i> </li> --}}
                      {{-- <li class="list-inline-item email-delete"> <i class='ti ti-trash'></i></li> --}}
                      <li class="list-inline-item" data-href="{{route('admin.projects.tasks.checklist-items.create', [$project, $task])}}" data-toggle="ajax-modal" data-title="Add Check Item"> <i class="tf-icons ti ti-plus scaleX-n1-rtl ti-sm"></i> </li>
                      <li class="list-inline-item" data-href="{{route('admin.projects.tasks.edit', [$project, $task])}}" data-toggle="ajax-modal" data-title="Edit Task"> <i class="ti ti-edit"></i> </li>
                      <li class="list-inline-item" data-href="{{route('admin.projects.tasks.destroy', [$project, $task])}}" data-toggle="ajax-delete"> <i class="ti ti-trash"></i> </li>
                    </ul>
                  </div>
                </div>
              </li>
              <div class="task-check-items" data-task-list="{{$task->id}}">
                @forelse ($task->checklistItems as $item)
                  <li class="email-list-item" data-item-id="{{$item->id}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
                    <div class="d-flex align-items-center">
                      <i class="drag-handle cursor-move fa-solid fa-ellipsis-vertical ms-5 me-2"></i>
                      <div class="form-check mb-0">
                        <input class="email-list-item-input form-check-input" type="checkbox" @checked($item->completed_by != null)>
                        <label class="form-check-label" for="email-1"></label>
                      </div>
                      {{-- <i class="email-list-item-bookmark ti ti-star ti-xs d-sm-inline-block d-none cursor-pointer ms-2 me-3"></i> --}}
                      <div class="email-list-item-content ms-2 ms-sm-0 me-2">
                        {{-- <span class="h6 email-list-item-username me-2">Chandler Bing</span> --}}
                        <span class="email-list-item-subject d-xl-inline-block d-block {{$item->completed_by != null ? 'text-decoration-line-through' : ''}}">{{$item->title}}</span>
                      </div>
                      <div class="email-list-item-meta ms-auto d-flex align-items-center">
                        <span class="badge bg-label-{{$colors[$task->priority]}} me-2">{{$task->priority}}</span>
                        @if ($item->assignedTo)
                          <img src="{{ $item->assignedTo->avatar }}" alt="user-avatar" class="d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
                        @endif
                        {{-- <small class="email-list-item-time text-muted">{{$task->created_at->diffForHumans()}}</small> --}}
                        <ul class="list-inline email-list-item-actions text-nowrap">
                          {{-- <li class="list-inline-item email-read"> <i class='ti ti-mail-opened'></i> </li> --}}
                          {{-- <li class="list-inline-item email-delete"> <i class='ti ti-trash'></i></li> --}}
                          <li class="list-inline-item" data-toggle="ajax-modal" data-title="Edit Check Item" data-href="{{route('admin.projects.tasks.checklist-items.edit', [$project, $task, 'checklist_item' => $item->id])}}"> <i class="ti ti-edit"></i> </li>
                          <li class="list-inline-item" data-toggle="ajax-delete"
                          data-href="{{ route('admin.projects.tasks.checklist-items.destroy', ['project' => $task->project_id, 'task' => $task, 'checklist_item' => $item->id]) }}"> <i class="ti ti-trash"></i> </li>
                        </ul>
                      </div>
                    </div>
                  </li>
                @empty
                @endforelse
              </div>
            @empty
            @endforelse
          </ul>
        </div>
      </div>
      <div class="app-overlay"></div>
    </div>
    <!-- /Emails List -->
  </div>
</div>
@endsection
