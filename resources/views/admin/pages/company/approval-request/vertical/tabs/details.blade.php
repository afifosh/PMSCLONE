<div class="col-12">
  <div class="nav-align-top nav-tabs-shadow h-100">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">Company Details</button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false">Activity Timeline</button>
      </li>
      <li class="nav-item">
        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-messages" aria-controls="navs-top-messages" aria-selected="false">Comments</button>
      </li>
    </ul>
    <div class="tab-content h-100">
      <div class="tab-pane fade show active" id="navs-top-home" role="tabpanel">
        <span class="custom-option-body">
          <div class="row">
            <input id="approval-status-1" name="approval_status[{{$detail['modification_id']}}]" type="hidden" value="1"/>
            {!! Form::hidden('modification_ids[]', $detail['modification_id']) !!}
            @forelse ($fields as $field_title => $field_name)
            @if ($field_name == 'poa')
              @continue
            @endif
                <div class="col-6 my-2">
                    <div class="fw-bold">
                      {{$field_title}}
                    </div>
                    <span class="fst-italic d-flex justify-content-between">
                      <span>{{ substr(is_array(@$detail[$field_name])? json_encode($detail[$field_name]) : $detail[$field_name],0 ,30) }}</span>
                      <div class="me-5">
                        <label class="switch switch-square">
                          <input type="checkbox" class="switch-input" data-switch-toggle-in-all="#rr-1" data-nset="#approval-status-1" checked />
                          <span class="switch-toggle-slider">
                            <span class="switch-on"><i class="ti ti-check"></i></span>
                            <span class="switch-off"><i class="ti ti-x"></i></span>
                          </span>
                        </label>
                      </div>
                    </span>
                </div>
            @empty
            @endforelse
            <div class="row d-none mt-2" id="rr-1">
              <div class="">
                <label class="form-label fw-bold">Rejection Reason</label>
                <textarea class="form-control" name="disapproval_reason[{{$detail['modification_id']}}]" rows="3"></textarea>
              </div>
            </div>
          </div>
        <hr class="my-5">
        <div class="row">
          <div class="col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Next</button>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
        @include('admin.pages.company.approval-request.vertical.tabs.activity-timeline')
      </div>
      <div class="tab-pane fade" id="navs-top-messages" role="tabpanel">
        @include('admin.pages.company.approval-request.vertical.tabs.comments')
      </div>
    </div>
  </div>
</div>
