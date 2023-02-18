@foreach ($row->data as $key => $item)
  <div class="row">
    <div class="col-sm-3"><strong>{{ ucfirst(str_replace("_","",$key)) }}</strong></div>
    <div class="col-sm-9">{{ $row->data[$key] }}</div>
  </div>
@endforeach
