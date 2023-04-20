@if ($isEditable)
  <form action="{{route('admin.approval-requests.level.companies.update', ['company' => $company->id, 'level' => $company->approval_level])}}" method="post">
    @csrf
    <input id="approval-status-{{$loop->iteration}}" name="approval_status[{{$doc['modification_id']}}]" type="hidden" value="1"/>
    {!! Form::hidden('modification_ids[]', $doc['modification_id']) !!}
@endif
  <div class="col-sm-12 mb-md-3">
    <div class="form-check custom-option custom-option-basic">
      <label class="form-check-label custom-option-content">
        <span class="custom-option-header mb-2">
          <span>
            <h6 class="fw-bold mb-0">{{ $requestedDocs->where('id', $doc['kyc_doc_id'])->first()->title }}</h6>
          </span>
          <span class="badge bg-label-{{ getCompanyStatusColor($status) }}">
            {{ucwords($status)}}
          </span>
        </span>
        <span class="custom-option-body">
          <div class="row">
            @forelse ($doc['fields'] as $field)
                <div class="col-6 my-1">
                    <div class="fw-semibold">
                      @if(@$modifications)
                      {{dd($modifications)}}
                      @endif
                      {{ $field['label'] }}
                      {{-- {{ $requestedDocs->where('id', $doc['kyc_doc_id'])->first()->fields[explode('_', $key)[3]]['label'] }} --}}
                    </div>
                    <span class="fst-italic d-flex justify-content-between">
                      {{-- {{dd($field)}} --}}
                      @if($field['type'] == 'file' && $field['value'])
                        <a href="{{ Storage::url($field['value']) }}" target="_blank" class="text-decoration-none">
                          <i class="fa-solid fa-file fa-lg me-1"></i>
                          Attachment
                        </a>
                      @else
                        {{ $field['value'] ?? 'N/A' }}
                      @endif
                    </span>
                </div>
            @empty
            @endforelse
            @if ($isEditable)
              <div class="row mt-2">
                <div class="">
                  <label class="form-label"><span class="fw-bold">Comment </span> (Optional) </label>
                  <textarea class="form-control" name="comment[{{$doc['modification_id']}}]" rows="3"></textarea>
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
