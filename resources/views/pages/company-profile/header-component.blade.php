<div class="content-header mb-3 d-sm-flex justify-content-between">
  <div>
    <h6 class="mb-0">{{$head_title}}</h6>
    <small>{{$head_sm}}</small>
  </div>
  <div>
    <div class="d-flex justify-content-between">
      <span class="">Setup Progress</span>
      <span>{{auth()->user()->company->step_completed_count}}/5</span>
    </div>
    <div class="progress" style="height:7px; width:300px">
      <div class="progress-bar" role="progressbar" style="width: {{(auth()->user()->company->step_completed_count/5)*100}}%" aria-valuenow="{{(auth()->user()->company->step_completed_count/5)*100}}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>
</div>
