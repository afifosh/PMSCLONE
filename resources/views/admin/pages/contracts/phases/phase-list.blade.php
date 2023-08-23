@forelse ($contract->phases as $phase)
  <li class="p-0 m-0 task task-item" data-id="{{$phase->id}}" data-{{slug($phase->status)}}="true">
    <div class="email-list-item todo-item d-flex align-items-center fw-bold task-header">
      <i class="el-voh drag-handle cursor-move fa-solid fa-ellipsis-vertical me-2"></i>
      <div class="email-list-item-content ms-2 ms-sm-0 me-2">
        <span class="email-list-item-subject d-xl-inline-block d-block">{{$phase->name}}</span>
      </div>
      <div class="email-list-item-meta ms-auto d-flex align-items-center">
        <span class="el-hoh badge bg-label-{{$colors[$phase->status]}} me-2" style="width: 113px;">{{$phase->status}}</span>
        <ul class="list-unstyled el-foh">
          <li class="m-0 me-2 p-0" data-href="{{route('admin.projects.contracts.phases.edit', ['project' => $project, $contract, $phase])}}" data-toggle="ajax-modal" data-title="Edit Phase"> <i class="ti ti-edit"></i> </li>
          <li class="m-0 me-2 p-0" data-href="{{route('admin.projects.contracts.phases.destroy', ['project' => $project, $contract, $phase])}}" data-toggle="ajax-delete"> <i class="ti ti-trash"></i> </li>
        </ul>
      </div>
    </div>
  </li>
@empty
@endforelse
