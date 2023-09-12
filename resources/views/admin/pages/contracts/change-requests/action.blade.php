<div class="d-inline-block text-nowrap">
  @if ($change_request->status == 'Pending')
  <button class="btn btn-sm btn-icon" data-toggle="confirm-action" data-confirm-btn="Approve" data-href="{{route('admin.contracts.change-requests.approve', ['contract' => $change_request->contract_id, 'change_request' => $change_request])}}"><i class="fa-regular fa-xl text-success fa-circle-check"></i></button>
  <button class="btn btn-sm btn-icon" data-toggle="confirm-action" data-confirm-btn="Reject" data-href="{{route('admin.contracts.change-requests.reject', ['contract' => $change_request->contract_id, 'change_request' => $change_request])}}"><i class="fa-regular fa-xl text-primary fa-circle-xmark"></i></i></button>
  @endif
  <button class="btn btn-sm btn-icon delete-record" data-toggle="ajax-delete"
      data-href="{{ route('admin.contracts.change-requests.destroy', ['contract' => $change_request->contract_id, 'change_request' => $change_request]) }}"><i class="ti ti-trash"></i></button>
</div>
