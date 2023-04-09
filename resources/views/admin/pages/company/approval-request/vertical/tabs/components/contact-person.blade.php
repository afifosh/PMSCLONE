@php
    $isEditable = !@$contact['id'];
    $status = 'pending';
    if(!isset($contact_original))
      $status = 'approved';
    if(isset($contact_original) && ($contact_original->approvals_count >= $level || $contact_original->disapprovals_count)) {
        $isEditable = false;
        if($contact_original->approvals_count >= $level){
          $status = 'approved';
        }elseif ($contact_original->disapprovals_count) {
          $status = 'rejected';
        }
    }
@endphp
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
                    </div>
                    <span class="fst-italic d-flex justify-content-between">
                      <span>{{ is_array(@$contact[$field_name])? json_encode($contact[$field_name]) : $contact[$field_name] }}</span>
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
                      <button type="button" data-disapprove="#approval-status-{{$loop->iteration}}" class="btn btn-outline-danger"><i class="fa-solid fa-xmark fa-lg me-1"></i> Reject</button>
                      <button type="button" data-approve="#approval-status-{{$loop->iteration}}" class="btn btn-outline-success"><i class="fa-solid fa-check fa-lg me-1"></i> Approve</button>
                      <button type="submit" class="d-none" data-form="ajax-form"></button>
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
