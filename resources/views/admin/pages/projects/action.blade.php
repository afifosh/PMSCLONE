<div class="d-inline-block text-nowrap">
  <a class="btn btn-sm btn-icon" href="{{route('admin.projects.edit', ['project' => $project])}}"><i class="ti ti-edit"></i></a>
      <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
          data-href="{{ route('admin.projects.destroy', ['project' => $project]) }}"><i
              class="ti ti-trash"></i></button>
</div>
