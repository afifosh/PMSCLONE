<div class="mt-3  col-12">
  <div class="card">
    <div class="p-3">
      <ul class="nav nav-pills flex-column flex-md-row">
        <li class="nav-item"><a class="nav-link {{ $tab == 'overview' ? 'active' : '' }}" href="{{route('admin.projects.show', $project)}}"><i class="fa fa-th menu-icon me-1"></i> Overview</a></li>
        <li class="nav-item"><a class="nav-link {{ $tab == 'tasks' ? 'active' : '' }}" href="{{route('admin.projects.tasks.index', $project)}}"><i class="fa-regular fa-check-circle menu-icon me-1"></i> Tasks</a></li>
        <li class="nav-item"><a class="nav-link {{ $tab == 'task-board' ? 'active' : '' }}" href="{{route('admin.projects.board-tasks.index', $project)}}"><i class="fa-regular fa-check-circle menu-icon me-1"></i> Tasks Board</a></li>
        {{-- <li class="nav-item"><a class="nav-link {{ $tab == 'stages' ? 'active' : '' }}" href="{{route('admin.projects.stages.index', $project)}}"><i class="fa-regular fa-check-circle menu-icon me-1"></i> Stages</a></li> --}}
        <li class="nav-item"><a class="nav-link {{ $tab == 'contracts' ? 'active' : '' }}" href="{{route('admin.projects.contracts.index', $project)}}"><i class="fa-regular fa-check-circle menu-icon me-1"></i> Contracts</a></li>
        <li class="nav-item"><a class="nav-link {{ $tab == 'gantt-chart' ? 'active' : '' }}" href="{{route('admin.projects.gantt-chart', $project)}}"><i class="fa-regular fa-check-circle menu-icon me-1"></i> Gantt Chart</a></li>
      </ul>
    </div>
  </div>
</div>
