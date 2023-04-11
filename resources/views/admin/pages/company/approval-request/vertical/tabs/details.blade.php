@php
    $isEditable = !@$detail['modifications']->isEmpty();
    $status = 'pending';
    if(is_array($detail['modifications']) && !$detail['modifications']->isEmpty()){
      $detail_original = $detail;
      $detail = transformModifiedData($detail->modifications);
      $detail['modification_id'] = $detail_original->id;
    }
    if(!isset($detail_original))
      $status = 'approved';
    if(isset($detail_original) && ($detail_original->approvals_count >= $level || $detail_original->disapprovals_count)) {
        $isEditable = false;
        if($detail_original->approvals_count >= $level){
          $status = 'approved';
        }elseif ($detail_original->disapprovals_count) {
          $status = 'rejected';
        }
    }
@endphp
<div class="col-12">
  <div class="card h-100">
      <div class="card-body">
        <div class="d-flex">
          <h5>Company Details</h5>
          <div class="ms-auto">
            <span class="badge bg-label-{{ getCompanyStatusColor($status) }}">{{ucwords($status)}}</span>
          </div>
        </div>
        <hr>
        <span class="custom-option-body">
          <form action="{{route('admin.approval-requests.level.companies.update', ['company' => $company->id, 'level' => $company->approval_level])}}" method="post">
            @if($errors->any())
                {!! implode('', $errors->all('<div>:message</div>')) !!}
            @endif
            @csrf
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
                        <span>{{ substr(is_array(@$detail[$field_name])? json_encode(@$detail[$field_name]) : @$detail[$field_name],0 ,30) }}</span>
                      </span>
                  </div>
              @empty
              @endforelse
              @if ($isEditable)
                <div class="row mt-2">
                  <div class="">
                    <label class="form-label"><span class="fw-bold">Comment </span> (Optional) </label>
                    <textarea class="form-control" name="comment[{{$detail['modification_id']}}]" rows="3"></textarea>
                    <div class="d-flex justify-content-end">
                      <div class="mt-2">
                        <button type="button" data-disapprove="#approval-status-1" class="btn btn-outline-danger"><i class="fa-solid fa-xmark fa-lg me-1"></i> Reject</button>
                        <button type="button" data-approve="#approval-status-1" class="btn btn-outline-success"><i class="fa-solid fa-check fa-lg me-1"></i> Approve</button>
                        <button type="submit" class="d-none" data-form="ajax-form"></button>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </form>
        <hr class="my-2">
        <div class="row">
          <div class="col-12 d-flex justify-content-end">
            <a href="{{ route('admin.approval-requests.level.companies.show', ['level' => request()->level, 'company' => request()->company, 'tab' => 'contact-persons']) }}" class="btn btn-primary">Next</a>
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
