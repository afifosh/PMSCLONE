<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Company" data-href="{{route('admin.companies.names.edit', [$historyName->model_id, $historyName->id])}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete" data-href="{{ route('admin.companies.names.destroy', [$historyName->model_id, $historyName->id]) }}"><i class="ti ti-trash"></i></button>
</div>
