
<div class="d-inline-block text-nowrap">
  <button class="btn btn-sm btn-icon" data-toggle="ajax-modal" data-title="Edit Document" data-href="{{route('admin.contracts.uploaded-documents.edit', [$contract, $doc])}}"><i class="ti ti-edit"></i></button>
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete" data-href="{{ route('admin.contracts.uploaded-documents.destroy', [$contract, $doc]) }}"><i class="ti ti-trash"></i></button>
  <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical"></i></button>
  <div class="dropdown-menu dropdown-menu-end m-0" style="">
    @forelse ($doc->versions as $version)
      <a href="javascript:;" data-toggle="ajax-modal" data-title="Document Details" data-href="{{ route('admin.contracts.uploaded-documents.show', [$contract, $version->id]) }}" class="dropdown-item">View Version {{$doc->versions_count - $loop->index}}</a>
    @empty
    @endforelse
  </div>
</div>
