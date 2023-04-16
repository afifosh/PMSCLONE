@if ($isEditable)
  <form action="{{route('admin.approval-requests.level.companies.update', ['company' => $company->id, 'level' => $company->approval_level])}}" method="post">
    @csrf
    <input id="approval-status-{{$loop->iteration}}" name="approval_status[{{$contact['modification_id']}}]" type="hidden" value="1"/>
    {!! Form::hidden('modification_ids[]', $contact['modification_id']) !!}
@endif
  <div class="col-sm-12 mb-md-3">
    <div class="form-check custom-option custom-option-basic">
      <label class="form-check-label custom-option-content">
        <span class="custom-option-header mb-2">
          <span>
            <h6 class="fw-semibold mb-0">{{@$contact['first_name']}} {{@$contact['last_name']}}</h6>
            <small>{{@$contact['position']}}</small>
          </span>
          <span class="badge bg-label-{{ getCompanyStatusColor($status) }}">
            {{ucwords($status)}}
          </span>
        </span>
        <span class="custom-option-body">
          <div class="row">
            @forelse ($fields as $field_title => $field_name)
            @if ($field_name == 'poa')
              @continue
            @endif
                <div class="col-6 my-1">
                    <div class="fw-bold">
                      {{$field_title}}
                      @if(@$modifications[$field_name])
                        <span class="text-warning"><i class="fa-solid fa-circle-exclamation fa-lg"></i></span>
                      @endif
                    </div>
                    <span class="fst-italic d-flex justify-content-between">
                      <span>
                        @if ($field_name == 'type')
                          {{$contactTypes[$contact[$field_name]]}}
                        @else
                          {{ $contact[$field_name] ?? 'N/A'}}
                        @endif
                      </span>
                    </span>
                </div>
            @empty
            @endforelse
            @if ($isEditable)
              <div class="row mt-2">
                <div class="">
                  <label class="form-label"><span class="fw-bold">Comment </span> (Optional) </label>
                  <textarea class="form-control" name="comment[{{$contact['modification_id']}}]" rows="3"></textarea>
                  <div class="d-flex justify-content-end">
                    <div class="mt-2">
                      <button type="button" data-form="ajax-form" data-preAjaxAction="setApprovalStatus" data-preAjaxParams='{"target" : "#approval-status-{{$loop->iteration}}", "val" : 0}' class="btn btn-outline-danger"><i class="fa-solid fa-xmark fa-lg me-1"></i> Reject</button>
                      <button type="button" data-form="ajax-form" data-preAjaxAction="setApprovalStatus" data-preAjaxParams='{"target" : "#approval-status-{{$loop->iteration}}", "val" : 1}' class="btn btn-outline-success"><i class="fa-solid fa-check fa-lg me-1"></i> Approve</button>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>
      </label>
    </div>
  </div>
@if ($isEditable)
  </form>
@endif
