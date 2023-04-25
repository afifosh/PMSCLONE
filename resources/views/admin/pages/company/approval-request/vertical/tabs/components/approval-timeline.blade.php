@if (count($approvals) || count($disapprovals))
  <hr>
  @forelse ($approvals as $approval)
  <div class="col-6 my-1">
      <div class="fst-italic text-success">
        Approved By {{$approval->approver->email}}. On {{date('d M Y', strtotime($approval->created_at))}}
      </div>
    </div>
  @empty
  @endforelse
  @forelse ($disapprovals as $disapproval)
  <div class="col-6 my-1">
      <div class="fst-italic text-danger">
        Rejected By {{$disapproval->disapprover->email}}. On {{date('d M Y', strtotime($disapproval->created_at))}}
      </div>
    </div>
  @empty
  @endforelse
@endif
