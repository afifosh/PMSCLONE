@extends('admin/layouts/layoutMaster')

@section('title', 'Task Templates')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/katex.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/app-email.css')}}" />
{{-- <link rel="stylesheet" href="{{asset('app-assets/css/pages/app-todo.css')}}" /> --}}
<style>
  .el-voh {
    visibility: hidden !important;
  }
  .email-list-item:hover .el-voh {
    visibility: visible !important;
  }
  .el-hoh {
    visibility: visible !important;
  }
  .email-list-item:hover .el-hoh {
    visibility: hidden !important;
  }

  .el-foh {
    display: none !important;
  }

  .email-list-item:hover .el-foh {
    display: flex !important;
  }

  .todo-item {
    border-bottom: 1px solid #dbdade;
    padding: 0.875rem 1rem;
    transition: all 0.15s ease-in-out;
    cursor: pointer;
    z-index: 1;
  }

  /* Hide by default */
.task.empty-task .subtasks {
    min-height: 0;
    background-color: transparent;
    border: none;
    transition: background-color 0.3s;
}
/* Hide by default */
.task.empty-task .subtasks {
    min-height: 0;
    background-color: transparent;
    border: none;
    transition: background-color 0.3s;
}
/* Show when dragging */
.dragging .task.empty-task .subtasks {
    min-height: 50px;
    background-color: #f5f5f5;
    border: 1px dashed #ccc;
}

/* Hover effect when dragging */
.dragging .task.empty-task:hover .subtasks {
    background-color: #e0e0e0;
}

/* Highlighting when dragging with Dragula */
.gu-mirror {
  position: fixed !important;
  margin: 0 !important;
  z-index: 9999 !important;
  opacity: 0.8;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";
  filter: alpha(opacity=80);
}
.gu-hide {
  display: none !important;
}
.gu-unselectable {
  -webkit-user-select: none !important;
  -moz-user-select: none !important;
  -ms-user-select: none !important;
  user-select: none !important;
}
.gu-transit {
  opacity: 0.2;
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=20)";
  filter: alpha(opacity=20);
}
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="https://rawgit.com/bevacqua/dragula/master/dist/dragula.js"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/custom/admin-project-taskboard.js')}}"></script>
<script src={{asset('assets/js/custom/select2.js')}}></script>
<script src={{asset('assets/js/custom/flatpickr.js')}}></script>
<script>
  window.active_project = '{{$project->id}}';
  function refreshTaskList(project_id){
    project_id = project_id || window.active_project;
    $.ajax({
      type: "get",
      url: route('admin.projects.board-tasks.index', {project: project_id}),
      success: function (response) {
        $('.tasks-list').html(response.data.view_data)
        $('.myTasksCount').text(response.data.myTasksCount)
      }
    });
  }

  function initDragola(){
    const drake = dragula([document.querySelector('.tasks'), ...document.querySelectorAll('.subtasks')], {
      moves: function (el, container, handle) {
          return handle.classList.contains('drag-handle');
          // if (el.classList.contains('subtask')) {
          //     return true;
          // }

          // if (el.classList.contains('task')) {
          //     return handle.classList.contains('task-header');
          // }

          // return false;
      },
      accepts: function (el, target, source, sibling) {
          if (el.classList.contains('task') && target.classList.contains('subtasks')) {
              return false;
          }

          if (el.classList.contains('subtask') && !target.classList.contains('subtasks')) {
              return false;
          }

          return true;
      }
    });
    drake.on('drag', function() {
      document.body.classList.add('dragging');
      updateEmptyTaskStatus();
    });

    drake.on('dragend', function() {
        document.body.classList.remove('dragging');
        updateEmptyTaskStatus();
    });
  }
  function updateEmptyTaskStatus() {
    const mainTasks = document.querySelectorAll('.task');

    mainTasks.forEach(task => {
        const subtasks = task.querySelectorAll('.subtask');
        if (subtasks.length === 0) {
            task.classList.add('empty-task');
        } else {
            task.classList.remove('empty-task');
        }
    });
  }

  $(document).ready(function () {
    initDragola();
  });

</script>
@endsection

@section('content')
@include('admin.pages.projects.navbar', ['tab' => 'task-board'])
<div class="app-email mt-3 card">
  <div class="row g-0">
    <!-- Task Sidebar -->
    <div class="col app-email-sidebar border-end flex-grow-0" id="app-email-sidebar">
      <div class="btn-compost-wrapper d-grid">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="Add Task" data-href="{{route('admin.projects.tasks.create', [$project, 'from' => 'task-board'])}}">Add Task</button>
      </div>
      <div class="email-filters py-2">
        <small class="fw-normal text-uppercase text-muted m-4">Tasks Status</small>
        <ul class="email-filter-folders list-unstyled mb-4">
          <li class="active d-flex justify-content-between" data-target="inbox">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">All</span>
            </a>
          </li>
          <li class="d-flex justify-content-between" data-target="mine">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
              <span class="align-middle ms-2">My Task</span>
            </a>
            <div class="badge bg-label-warning rounded-pill badge-center">{{$myTasksCount}}</div>
          </li>
          @forelse ($task_statuses as $status)
            <li class="d-flex justify-content-between" data-target="{{slug($status)}}">
              <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                <span class="align-middle ms-2">{{$status}}</span>
              </a>
              <div class="badge bg-label-warning rounded-pill badge-center">{{$project->tasks->where('status', $status)->count()}}</div>
            </li>
          @empty
          @endforelse
        </ul>
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
              <div class="badge bg-label-{{$colors[$priority]}} rounded-pill badge-center">{{$project->tasks->where('priority', $priority)->count()}}</div>
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
                  <input type="text" class="form-control email-search-input border-0" placeholder="Search Task">
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center mb-0 mb-md-2">
              <i class="ti ti-rotate-clockwise rotate-180 scaleX-n1-rtl cursor-pointer email-refresh me-2 mt-1" onclick="refreshTaskList('{{$project->id}}')"></i>
            </div>
          </div>
        </div>
        <hr class="container-m-nx m-0">
        <!-- Task List: Items -->
        {{-- <div class="email-list pt-0"> --}}
          {{-- <ul class="list-unstyled m-0 tasks-list tasks"> --}}
            @include('admin.pages.projects.task-board.tasks-list')
          {{-- </ul> --}}
        {{-- </div> --}}
      </div>
      <div class="app-overlay"></div>
    </div>
    <!-- /Task List -->
  </div>
</div>
@endsection
