<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Type" data-href="{{route('admin.contract-categories.edit', $contract_category)}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.contract-categories.destroy', $contract_category) }}"><i class="ti ti-trash"></i></button>
</div>
