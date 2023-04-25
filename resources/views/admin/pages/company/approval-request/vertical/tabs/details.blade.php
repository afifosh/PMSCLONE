@php
  $detail_original = $detail;
  $modifications = [];
  $approvals = $POCDetail ? $POCDetail->approvals : (@$detail->modifications[0] ? $detail->modifications[0]->approvals : []);
  $disapprovals = $POCDetail ? $POCDetail->disapprovals : (@$detail->modifications[0] ? $detail->modifications[0]->disapprovals : []);
  if (!is_array($detail) && $detail_original && $detail->modifications->count()) {
    $modifications = transformModifiedData($detail->modifications[0]->modifications);
    $detail = $modifications + $detail->toArray();
    $detail['modification_id'] = $detail_original->modifications[0]->id;
  }
  $status = $detailsStatus;
  $status_color = getCompanyStatusColor($status);
  $isEditable = $status_color == 'warning';
@endphp
<div class="col-12">
  <div class="card h-100">
      <div class="card-body">
        <div class="d-flex">
          <h5>Company Details</h5>
          <div class="ms-auto">
            <span class="badge bg-label-{{ $status_color }}">{{ucwords($status)}}</span>
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
                        @if(@$modifications[$field_name])
                          <span class="text-warning"><i class="fa-solid fa-circle-exclamation fa-lg"></i></span>
                        @endif
                      </div>
                      <span class="fst-italic d-flex justify-content-between">
                        <span>
                          @if ($field_name == 'locality_type')
                            {{@$localityTypes[$detail[$field_name]]}}
                          @elseif ($field_name == 'no_of_employees')
                            {{$NoOfEmployee[$detail[$field_name]]}}
                          @elseif ($field_name == 'logo')
                            <img src="{{$company->getPOCLogoUrl()}}" alt="" height="100px" width="100px">
                          @elseif ($field_name == 'legal_form')
                            {{@$legalForms[$detail[$field_name]]}}
                          @elseif ($field_name == 'geographical_coverage')
                            @php
                              $detail[$field_name] = array_filter($detail[$field_name], function($value) {
                                  return $value !== null;
                              });
                              $array = array_map(function($element) use ($countries) {
                                  return $countries[$element];
                              }, $detail[$field_name]);
                              $geo_cov = implode(", ", $array);
                              $geo_cov = $geo_cov == '' ? 'N/A' : $geo_cov;
                            @endphp
                            {{@$geo_cov ?? 'N/A'}}
                          @elseif ($field_name == 'subsidiaries')
                            {{is_array(@$detail[$field_name]) ? @implode(", ", @$detail[$field_name]) : 'N/A'}}
                          @else
                            {{ (is_array(@$detail[$field_name])? json_encode(@$detail[$field_name]) :(@$detail[$field_name] ?? 'N/A')) }}
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
                    <textarea class="form-control" name="comment[{{$detail['modification_id']}}]" rows="3"></textarea>
                    <div class="d-flex justify-content-end">
                      <div class="mt-2">
                        <button type="button" data-form="ajax-form" data-preAjaxAction="setApprovalStatus" data-preAjaxParams='{"target" : "#approval-status-1", "val" : 0}' class="btn btn-outline-danger"><i class="fa-solid fa-xmark fa-lg me-1"></i> Reject</button>
                        <button type="button" data-form="ajax-form" data-preAjaxAction="setApprovalStatus" data-preAjaxParams='{"target" : "#approval-status-1", "val" : 1}' class="btn btn-outline-success"><i class="fa-solid fa-check fa-lg me-1"></i> Approve</button>
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
