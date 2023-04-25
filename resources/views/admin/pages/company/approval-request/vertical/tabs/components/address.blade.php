@if ($isEditable)
  <form action="{{route('admin.approval-requests.level.companies.update', ['company' => $company->id, 'level' => $company->approval_level])}}" method="post">
    @csrf
    <input id="approval-status-{{$loop->iteration}}" name="approval_status[{{$address['modification_id']}}]" type="hidden" value="1"/>
    {!! Form::hidden('modification_ids[]', $address['modification_id']) !!}
@endif
  <div class="col-sm-12 mb-md-3">
    <div class="form-check custom-option custom-option-basic">
      <label class="form-check-label custom-option-content">
        <span class="custom-option-header mb-2">
          <span>
            <h6 class="fw-semibold mb-0">{{ @$address['name'] }}</h6>
          </span>
          <span class="badge bg-label-{{ getCompanyStatusColor($status) }}">
            {{ucwords($status)}}
          </span>
        </span>
        <span class="custom-option-body">
          <div class="row">
            @forelse ($fields as $field_title => $field_name)
                <div class="col-6 my-1">
                    <div class="fw-bold">
                      {{$field_title}}
                      @if(@$modifications[$field_name])
                        <span class="text-warning"><i class="fa-solid fa-circle-exclamation fa-lg"></i></span>
                      @endif
                    </div>
                    <span class="fst-italic d-flex justify-content-between">
                      <span>
                        @if ($field_name == 'address_type')
                          @php
                              $address[$field_name] = array_filter($address[$field_name], function($value) {
                                  return $value !== null;
                              });
                              $array = array_map(function($element) use ($addressTypes) {
                                  return $element != null ? $addressTypes[$element] : '';
                              }, $address[$field_name]);
                              $type = implode(", ", $array);
                              $type = $type == '' ? 'N/A' : $type;
                          @endphp
                          {{$type}}
                        @elseif ($field_name == 'country_id')
                          {{ @$countries[$address[$field_name]] }}
                        @else
                          {{ is_array(@$address[$field_name])? json_encode($address[$field_name]) : $address[$field_name] }}
                        @endif
                      </span>
                    </span>
                </div>
            @empty
            @endforelse
            @include('admin.pages.company.approval-request.vertical.tabs.components.approval-timeline')
            @if ($isEditable)
              <div class="row mt-2">
                <div class="">
                  <label class="form-label"><span class="fw-bold">Comment </span> (Optional) </label>
                  <textarea class="form-control" name="comment[{{$address['modification_id']}}]" rows="3"></textarea>
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
        </span>
      </label>
    </div>
  </div>
@if ($isEditable)
  </form>
@endif
