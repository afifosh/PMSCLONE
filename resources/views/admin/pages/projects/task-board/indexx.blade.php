@extends('admin/layouts/layoutMaster')

@section('title', 'Task Templates')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-email.css') }}" />
    <link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-todo.css') }}" />
    {{-- <style>
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
</style> --}}
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/block-ui/block-ui.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sortablejs/sortable.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="https://rawgit.com/bevacqua/dragula/master/dist/dragula.js"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/custom/admin-project-taskboard.js') }}"></script>
    <script src={{ asset('assets/js/custom/select2.js') }}></script>
    <script src={{ asset('assets/js/custom/flatpickr.js') }}></script>
    <script>
        window.active_project = '{{ $project->id }}';

        function refreshTaskList(project_id) {
            project_id = project_id || window.active_project;
            $.ajax({
                type: "get",
                url: route('admin.projects.board-tasks.index', {
                    project: project_id
                }),
                success: function(response) {
                    $('.tasks-list').html(response.data.view_data)
                    $('.myTasksCount').text(response.data.myTasksCount)
                }
            });
        }

        function initDragola() {
            const drake = dragula([document.querySelector('.tasks'), ...document.querySelectorAll('.subtasks')], {
                moves: function(el, container, handle) {
                    return handle.classList.contains('drag-handle');
                    // if (el.classList.contains('subtask')) {
                    //     return true;
                    // }

                    // if (el.classList.contains('task')) {
                    //     return handle.classList.contains('task-header');
                    // }

                    // return false;
                },
                accepts: function(el, target, source, sibling) {
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

        $(document).ready(function() {
            initDragola();
        });
    </script>
@endsection
@section('content')
    <div class="app-content content todo-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper container-xxl p-0 d-flex">
            <div class="sidebar-left">
                <div class="sidebar">
                    <div class="sidebar-content todo-sidebar">
                        <div class="todo-app-menu">
                            <div class="add-task">
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#new-task-modal">
                                    Add Task
                                </button>
                            </div>
                            <div class="sidebar-menu-list">
                                <div class="list-group list-group-filters">
                                    <a href="#" class="list-group-item list-group-item-action active">
                                        <i data-feather="mail" class="font-medium-3 me-50"></i>
                                        <span class="align-middle"> My Task</span>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i data-feather="star" class="font-medium-3 me-50"></i> <span
                                            class="align-middle">Important</span>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i data-feather="check" class="font-medium-3 me-50"></i> <span
                                            class="align-middle">Completed</span>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i data-feather="trash" class="font-medium-3 me-50"></i> <span
                                            class="align-middle">Deleted</span>
                                    </a>
                                </div>
                                <div class="mt-3 px-2 d-flex justify-content-between">
                                    <h6 class="section-label mb-1">Tags</h6>
                                    <i data-feather="plus" class="cursor-pointer"></i>
                                </div>
                                <div class="list-group list-group-labels">
                                    <a href="#"
                                        class="list-group-item list-group-item-action d-flex align-items-center">
                                        <span class="bullet bullet-sm bullet-primary me-1"></span>Team
                                    </a>
                                    <a href="#"
                                        class="list-group-item list-group-item-action d-flex align-items-center">
                                        <span class="bullet bullet-sm bullet-success me-1"></span>Low
                                    </a>
                                    <a href="#"
                                        class="list-group-item list-group-item-action d-flex align-items-center">
                                        <span class="bullet bullet-sm bullet-warning me-1"></span>Medium
                                    </a>
                                    <a href="#"
                                        class="list-group-item list-group-item-action d-flex align-items-center">
                                        <span class="bullet bullet-sm bullet-danger me-1"></span>High
                                    </a>
                                    <a href="#"
                                        class="list-group-item list-group-item-action d-flex align-items-center">
                                        <span class="bullet bullet-sm bullet-info me-1"></span>Update
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="content-right">
                <div class="content-wrapper container-xxl p-0">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        <div class="body-content-overlay"></div>
                        <div class="todo-app-list">
                            <!-- Todo search starts -->
                            <div class="app-fixed-search d-flex align-items-center">
                                <div class="sidebar-toggle d-block d-lg-none ms-1">
                                    <i data-feather="menu" class="font-medium-5"></i>
                                </div>
                                <div class="d-flex align-content-center justify-content-between w-100">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i data-feather="search"
                                                class="text-muted"></i></span>
                                        <input type="text" class="form-control" id="todo-search"
                                            placeholder="Search task" aria-label="Search..."
                                            aria-describedby="todo-search" />
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle hide-arrow me-1" id="todoActions"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-vertical" class="font-medium-2 text-body"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="todoActions">
                                        <a class="dropdown-item sort-asc" href="#">Sort A - Z</a>
                                        <a class="dropdown-item sort-desc" href="#">Sort Z - A</a>
                                        <a class="dropdown-item" href="#">Sort Assignee</a>
                                        <a class="dropdown-item" href="#">Sort Due Date</a>
                                        <a class="dropdown-item" href="#">Sort Today</a>
                                        <a class="dropdown-item" href="#">Sort 1 Week</a>
                                        <a class="dropdown-item" href="#">Sort 1 Month</a>
                                    </div>
                                </div>
                            </div>
                            <!-- Todo search ends -->

                            <!-- Todo List starts -->
                            <div class="todo-task-list-wrapper">
                                <ul class="todo-task-list">
                                    <li class="todo-item task">
                                        <div class="todo-title-wrapper">
                                            <div class="todo-title-area">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-more-vertical drag-icon">
                                                    <circle cx="12" cy="12" r="1"></circle>
                                                    <circle cx="12" cy="5" r="1"></circle>
                                                    <circle cx="12" cy="19" r="1"></circle>
                                                </svg>
                                                <div class="title-wrapper">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="customCheck1">
                                                        <label class="form-check-label" for="customCheck1"></label>
                                                    </div>
                                                    <span class="todo-title">Main Task 1</span>
                                                </div>
                                            </div>
                                            <div class="todo-item-action">
                                                <div class="badge-wrapper me-1">
                                                    <span class="badge rounded-pill badge-light-primary">Team</span>
                                                </div>
                                                <small class="text-nowrap text-muted me-1">Aug 08</small>
                                                <div class="avatar">
                                                    <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg"
                                                        alt="user-avatar" height="32" width="32">
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="todo-task-list subtasks">
                                            <li class="todo-item subtask pe-0" style="">
                                                <div class="todo-title-wrapper">
                                                    <div class="todo-title-area">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="feather feather-more-vertical drag-icon">
                                                            <circle cx="12" cy="12" r="1">
                                                            </circle>
                                                            <circle cx="12" cy="5" r="1">
                                                            </circle>
                                                            <circle cx="12" cy="19" r="1">
                                                            </circle>
                                                        </svg>
                                                        <div class="title-wrapper">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="customCheck1">
                                                                <label class="form-check-label"
                                                                    for="customCheck1"></label>
                                                            </div>
                                                            <span class="todo-title">Sub Task 1.1</span>
                                                        </div>
                                                    </div>
                                                    <div class="todo-item-action">
                                                        <div class="badge-wrapper me-1">
                                                            <span
                                                                class="badge rounded-pill badge-light-primary">Team</span>
                                                        </div>
                                                        <small class="text-nowrap text-muted me-1">Aug 08</small>
                                                        <div class="avatar">
                                                            <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg"
                                                                alt="user-avatar" height="32" width="32">
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="todo-item subtask pe-0" style="">
                                                <div class="todo-title-wrapper">
                                                    <div class="todo-title-area">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="feather feather-more-vertical drag-icon">
                                                            <circle cx="12" cy="12" r="1">
                                                            </circle>
                                                            <circle cx="12" cy="5" r="1">
                                                            </circle>
                                                            <circle cx="12" cy="19" r="1">
                                                            </circle>
                                                        </svg>
                                                        <div class="title-wrapper">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="customCheck1">
                                                                <label class="form-check-label"
                                                                    for="customCheck1"></label>
                                                            </div>
                                                            <span class="todo-title">Sub Task 1.2</span>
                                                        </div>
                                                    </div>
                                                    <div class="todo-item-action">
                                                        <div class="badge-wrapper me-1">
                                                            <span
                                                                class="badge rounded-pill badge-light-primary">Team</span>
                                                        </div>
                                                        <small class="text-nowrap text-muted me-1">Aug 08</small>
                                                        <div class="avatar">
                                                            <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg"
                                                                alt="user-avatar" height="32" width="32">
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="todo-item task">
                                        <div class="todo-title-wrapper">
                                            <div class="todo-title-area">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-more-vertical drag-icon">
                                                    <circle cx="12" cy="12" r="1"></circle>
                                                    <circle cx="12" cy="5" r="1"></circle>
                                                    <circle cx="12" cy="19" r="1"></circle>
                                                </svg>
                                                <div class="title-wrapper">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="customCheck1">
                                                        <label class="form-check-label" for="customCheck1"></label>
                                                    </div>
                                                    <span class="todo-title">Main Task 2</span>
                                                </div>
                                            </div>
                                            <div class="todo-item-action">
                                                <div class="badge-wrapper me-1">
                                                    <span class="badge rounded-pill badge-light-primary">Team</span>
                                                </div>
                                                <small class="text-nowrap text-muted me-1">Aug 08</small>
                                                <div class="avatar">
                                                    <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg"
                                                        alt="user-avatar" height="32" width="32">
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="todo-task-list  subtasks">
                                            <li class="todo-item subtask pe-0" style="">
                                                <div class="todo-title-wrapper">
                                                    <div class="todo-title-area">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="feather feather-more-vertical drag-icon">
                                                            <circle cx="12" cy="12" r="1">
                                                            </circle>
                                                            <circle cx="12" cy="5" r="1">
                                                            </circle>
                                                            <circle cx="12" cy="19" r="1">
                                                            </circle>
                                                        </svg>
                                                        <div class="title-wrapper">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="customCheck1">
                                                                <label class="form-check-label"
                                                                    for="customCheck1"></label>
                                                            </div>
                                                            <span class="todo-title">Sub Task 2.1</span>
                                                        </div>
                                                    </div>
                                                    <div class="todo-item-action">
                                                        <div class="badge-wrapper me-1">
                                                            <span
                                                                class="badge rounded-pill badge-light-primary">Team</span>
                                                        </div>
                                                        <small class="text-nowrap text-muted me-1">Aug 08</small>
                                                        <div class="avatar">
                                                            <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg"
                                                                alt="user-avatar" height="32" width="32">
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="todo-item subtask pe-0" style="">
                                                <div class="todo-title-wrapper">
                                                    <div class="todo-title-area">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="feather feather-more-vertical drag-icon">
                                                            <circle cx="12" cy="12" r="1">
                                                            </circle>
                                                            <circle cx="12" cy="5" r="1">
                                                            </circle>
                                                            <circle cx="12" cy="19" r="1">
                                                            </circle>
                                                        </svg>
                                                        <div class="title-wrapper">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="customCheck1">
                                                                <label class="form-check-label"
                                                                    for="customCheck1"></label>
                                                            </div>
                                                            <span class="todo-title">Sub Task 2.2</span>
                                                        </div>
                                                    </div>
                                                    <div class="todo-item-action">
                                                        <div class="badge-wrapper me-1">
                                                            <span
                                                                class="badge rounded-pill badge-light-primary">Team</span>
                                                        </div>
                                                        <small class="text-nowrap text-muted me-1">Aug 08</small>
                                                        <div class="avatar">
                                                            <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg"
                                                                alt="user-avatar" height="32" width="32">
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <!-- More list items for tasks and subtasks can be added as needed -->
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Todo List ends -->
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    {{-- <div class="sidenav-overlay"></div>
<div class="drag-target"></div> --}}
@endsection
