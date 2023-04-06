<div class="card-body">
  <h5>Contact Persons</h5>
  @forelse ($fields as $field_title => $field_name)
    <div class="row">
      <span>
          <strong>{{$field_title}} : </strong>
      </span>
      <div class="row mx-2">
        <div class="mt-2 d-flex justify-content-between">
          <div>
            <div class="fst-italic">Current</div>
            <div class="fw-bold">CName test</div>
          </div>
          <div>
            <div class="fst-italic">New</div>
            <div class="fw-bold">CName test new</div>
          </div>
          <div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="{{$field_name}}_status" id="{{$field_name}}-app" value="true" data-radio-toggle-in="#{{$field_name}}-rr" checked>
              <label class="form-check-label" for="{{$field_name}}-app">
                Approve
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="{{$field_name}}_status" data-radio-toggle-in="#{{$field_name}}-rr" value="false" id="{{$field_name}}-re" >
              <label class="form-check-label" for="{{$field_name}}-re">
                Reject
              </label>
            </div>
          </div>
        </div>
        <div class="row d-none" id="{{$field_name}}-rr">
          <div class="my-2">
            <label for="reason" class="form-label fw-bold">Rejection Reason</label>
            <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
          </div>
        </div>
      </div>
    </div>
    <hr>
  @empty
  @endforelse
  <div class="row">
    <div class="col-12 d-flex justify-content-end">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </div>
</div>
