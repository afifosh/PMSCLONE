{{-- @forelse ($project->tasks as $task)
  <li class="p-0 m-0" data-mine="{{$task->assignees->where('id', auth()->id())->count() > 0}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
    <div class="email-list-item todo-item d-flex align-items-center fw-bold task-header task" style="background-color: rgba(75, 70, 92, 0.04)">
      <i class="el-voh drag-handle cursor-move fa-solid fa-ellipsis-vertical me-2"></i>
      <div class="email-list-item-content ms-2 ms-sm-0 me-2">
        <span class="email-list-item-subject d-xl-inline-block d-block">{{$task->subject}}</span>
      </div>
      <div class="email-list-item-meta ms-auto d-flex align-items-center">
        <span class="el-hoh badge bg-label-{{$colors[$task->priority]}} me-2">{{$task->priority}}</span>
        @isset($task->assignees[0])
        <img src="{{ $task->assignees[0]->avatar }}" alt="user-avatar" class="el-hoh d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
        @endisset
        <ul class="list-unstyled el-foh">
          <li class="m-0 me-2 p-0" data-href="{{route('admin.projects.tasks.checklist-items.create', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-modal" data-title="Add Check Item"> <i class="tf-icons ti ti-plus scaleX-n1-rtl ti-sm"></i> </li>
          <li class="m-0 me-2 p-0" data-href="{{route('admin.projects.tasks.edit', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-modal" data-title="Edit Task"> <i class="ti ti-edit"></i> </li>
          <li class="m-0 me-2 p-0" data-href="{{route('admin.projects.tasks.destroy', [$project, $task, 'from' => 'task-board'])}}" data-toggle="ajax-delete"> <i class="ti ti-trash"></i> </li>
        </ul>
      </div>
    </div>
    <ul class="list-unstyled task-check-items subtasks" data-task-list="{{$task->id}}">
      @forelse ($task->checklistItems as $item)
        <li class="email-list-item subtask" data-item-id="{{$item->id}}" data-{{slug($task->status)}}="true" data-{{$task->priority}}="true">
          <div class="d-flex align-items-center">
            <i class="el-voh drag-handle cursor-move fa-solid fa-ellipsis-vertical me-2"></i>
            <div class="form-check mb-0">
              <input class="email-list-item-input form-check-input checklist-status" type="checkbox" @checked($item->completed_by != null)>
              <label class="form-check-label" for="email-1"></label>
            </div>
            <div class="email-list-item-content ms-2 ms-sm-0 me-2">
              <span class="email-list-item-subject d-xl-inline-block d-block {{$item->completed_by != null ? 'text-decoration-line-through' : ''}}">{{$item->title}}</span>
            </div>
            <div class="email-list-item-meta ms-auto d-flex align-items-center">
              @if ($item->assignedTo)
                <img src="{{ $item->assignedTo->avatar }}" alt="user-avatar" class="el-hoh d-block flex-shrink-0 rounded-circle me-sm-3 me-2" height="32" width="32" />
              @endif
              <ul class="list-unstyled el-foh">
                <li class="m-0 me-2 p-0" data-toggle="ajax-modal" data-title="Edit Check Item" data-href="{{route('admin.projects.tasks.checklist-items.edit', [$project, $task, 'checklist_item' => $item->id, 'from' => 'task-board'])}}"> <i class="ti ti-edit"></i> </li>
                <li class="m-0 me-2 p-0" data-toggle="ajax-delete"
                data-href="{{ route('admin.projects.tasks.checklist-items.destroy', ['project' => $task->project_id, 'task' => $task, 'checklist_item' => $item->id, 'from' => 'task-board']) }}"> <i class="ti ti-trash"></i> </li>
              </ul>
            </div>
          </div>
        </li>
      @empty
      @endforelse
    </ul>
  </li>
@empty
@endforelse --}}
<div class="todo-task-list-wrapper">
  <ul class="todo-task-list"><li class="todo-item task">
                      <div class="todo-title-wrapper">
                          <div class="todo-title-area">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical drag-icon"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                              <div class="title-wrapper">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck1">
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
                                  <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg" alt="user-avatar" height="32" width="32">
                              </div>
                          </div>
                      </div>
                  <ul class="todo-task-list subtasks">
              <li class="todo-item subtask pe-0" style="">
                      <div class="todo-title-wrapper">
                          <div class="todo-title-area">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical drag-icon"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                              <div class="title-wrapper">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck1">
                                      <label class="form-check-label" for="customCheck1"></label>
                                  </div>
                                  <span class="todo-title">Sub Task 1.1</span>
                              </div>
                          </div>
                          <div class="todo-item-action">
                              <div class="badge-wrapper me-1">
                                  <span class="badge rounded-pill badge-light-primary">Team</span>
                              </div>
                              <small class="text-nowrap text-muted me-1">Aug 08</small>
                              <div class="avatar">
                                  <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg" alt="user-avatar" height="32" width="32">
                              </div>
                          </div>
                      </div>
                  </li>
              <li class="todo-item subtask pe-0" style="">
                      <div class="todo-title-wrapper">
                          <div class="todo-title-area">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical drag-icon"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                              <div class="title-wrapper">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck1">
                                      <label class="form-check-label" for="customCheck1"></label>
                                  </div>
                                  <span class="todo-title">Sub Task 1.2</span>
                              </div>
                          </div>
                          <div class="todo-item-action">
                              <div class="badge-wrapper me-1">
                                  <span class="badge rounded-pill badge-light-primary">Team</span>
                              </div>
                              <small class="text-nowrap text-muted me-1">Aug 08</small>
                              <div class="avatar">
                                  <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg" alt="user-avatar" height="32" width="32">
                              </div>
                          </div>
                      </div>
                  </li>
          </ul></li><li class="todo-item task">
                      <div class="todo-title-wrapper">
                          <div class="todo-title-area">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical drag-icon"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                              <div class="title-wrapper">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck1">
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
                                  <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg" alt="user-avatar" height="32" width="32">
                              </div>
                          </div>
                      </div>
                  <ul class="todo-task-list  subtasks">
              <li class="todo-item subtask pe-0" style="">
                      <div class="todo-title-wrapper">
                          <div class="todo-title-area">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical drag-icon"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                              <div class="title-wrapper">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck1">
                                      <label class="form-check-label" for="customCheck1"></label>
                                  </div>
                                  <span class="todo-title">Sub Task 2.1</span>
                              </div>
                          </div>
                          <div class="todo-item-action">
                              <div class="badge-wrapper me-1">
                                  <span class="badge rounded-pill badge-light-primary">Team</span>
                              </div>
                              <small class="text-nowrap text-muted me-1">Aug 08</small>
                              <div class="avatar">
                                  <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg" alt="user-avatar" height="32" width="32">
                              </div>
                          </div>
                      </div>
                  </li>
              <li class="todo-item subtask pe-0" style="">
                      <div class="todo-title-wrapper">
                          <div class="todo-title-area">
                              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical drag-icon"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                              <div class="title-wrapper">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input" id="customCheck1">
                                      <label class="form-check-label" for="customCheck1"></label>
                                  </div>
                                  <span class="todo-title">Sub Task 2.2</span>
                              </div>
                          </div>
                          <div class="todo-item-action">
                              <div class="badge-wrapper me-1">
                                  <span class="badge rounded-pill badge-light-primary">Team</span>
                              </div>
                              <small class="text-nowrap text-muted me-1">Aug 08</small>
                              <div class="avatar">
                                  <img src="../../../app-assets/images/portrait/small/avatar-s-4.jpg" alt="user-avatar" height="32" width="32">
                              </div>
                          </div>
                      </div>
                  </li>
          </ul></li>



      <!-- More list items for tasks and subtasks can be added as needed -->
  </ul>
</div>
