<div class="content-header mb-3 d-sm-flex justify-content-between">
  <div>
    <h6 class="mb-0">{{$head_title}}</h6>
    <small>{{$head_sm}}</small>
  </div>
  <div>
    @isset($add_new)
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary" data-toggle="ajax-modal" data-title="{{$add_title}}" data-href="{{$add_new}}">Add New</button>
      </div>
    @endisset
  </div>
</div>
