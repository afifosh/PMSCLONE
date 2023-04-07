<div class="{{ isset($body_col) ? $body_col : 'col-9'}}">
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
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th scope="col">Attributes</th>
                  <th scope="col">Current Value</th>
                  <th scope="col">New Value</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($fields as $field_title => $field_name)
                  <tr class="">
                    <td>
                      <span class="fw-bold d-flex">
                        {{$field_title}} :
                      </span>
                    </td>
                    <td> <span class="fst-italic text-decoration-line-through col-4">{{ substr(is_array($detail[$field_name])? json_encode($detail[$field_name]) : $detail[$field_name],0 ,30) }}</span></td>
                    <td class="d-flex justify-content-between">
                      <span>{{substr(is_array($detail[$field_name])? json_encode($detail[$field_name]) : $detail[$field_name], 0, 30) }}</span>
                      <div class="me-5">
                        <label class="switch switch-square">
                          <input type="checkbox" class="switch-input" data-switch-toggle-in-all="#rr-1" checked />
                          <span class="switch-toggle-slider">
                            <span class="switch-on"><i class="ti ti-check"></i></span>
                            <span class="switch-off"><i class="ti ti-x"></i></span>
                          </span>
                        </label>
                      </div>
                    </td>
                  </tr>
                @empty
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="row d-none mt-2" id="rr-1">
            <div class="">
              <label for="reason" class="form-label fw-bold">Rejection Reason</label>
              <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
            </div>
          </div>
          <div class="row my-5">
            <div class="col-12 d-flex justify-content-between">
              <span class="fw-bolder col-4">Attributes </span>
              <span class="fw-bolder col-4">Old Value </span>
              <span class="fw-bolder col-4">New Value </span>
            </div>
            <hr>
            @forelse ($fields as $field_title => $field_name)
                <div class="col-12 d-flex justify-content-between">
                    <span class="fw-bold col-4 d-flex">
                      {{$field_title}} :
                    </span>
                    <span class="fst-italic text-decoration-line-through col-4">{{ substr(is_array($detail[$field_name])? json_encode($detail[$field_name]) : $detail[$field_name],0 ,30) }} </span>
                    <span class="fst-italic col-4 d-flex justify-content-between">
                      <span>{{ substr(is_array($detail[$field_name])? json_encode($detail[$field_name]) : $detail[$field_name],0 ,30) }}</span>
                      <div class="me-5">
                        <label class="switch switch-square">
                          <input type="checkbox" class="switch-input" data-switch-toggle-in-all="#rr-2" checked />
                          <span class="switch-toggle-slider">
                            <span class="switch-on"><i class="ti ti-check"></i></span>
                            <span class="switch-off"><i class="ti ti-x"></i></span>
                          </span>
                        </label>
                      </div>
                    </span>
                </div>
                <hr>
            @empty
            @endforelse
            <div class="row d-none mt-2" id="rr-2">
              <div class="">
                <label for="reason" class="form-label fw-bold">Rejection Reason</label>
                <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
              </div>
            </div>
          </div>
          <hr class="my-5">
          <div class="row my-5">
            @forelse ($fields as $field_title => $field_name)
                <div class="col-6 d-flex justify-content-between">
                    <span class="fw-bold">
                      {{$field_title}} :
                    </span>
                    <span class="fst-italic d-flex justify-content-between">
                      <span>{{ substr(is_array($detail[$field_name])? json_encode($detail[$field_name]) : $detail[$field_name],0 ,20) }}</span>
                      <div class="me-5">
                        <label class="switch switch-square">
                          <input type="checkbox" class="switch-input" data-switch-toggle-in-all="#rr-3" checked />
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
            <div class="row d-none mt-2" id="rr-3">
              <div class="">
                <label for="reason" class="form-label fw-bold">Rejection Reason</label>
                <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
              </div>
            </div>
          </div>
          <hr>
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
</div>
