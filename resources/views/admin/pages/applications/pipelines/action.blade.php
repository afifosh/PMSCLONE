<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-title={{__('Edit Application Type')}} data-toggle="ajax-modal" data-href="{{ route('admin.applications.settings.pipelines.edit', $applicationPipeline) }}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon {{!$applicationPipeline->applications_count ?: 'disabled'}}" data-toggle="ajax-delete"
      data-href="{{ route('admin.applications.settings.pipelines.destroy', $applicationPipeline) }}"><i class="ti ti-trash"></i></button>
</div>
