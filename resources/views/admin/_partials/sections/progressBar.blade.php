<div class="progress w-100" style="height:{{$height ?? '10px'}};"  tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="{{$perc}}%">
    <div class="progress-bar bg-{{$color}}"
        role="progressbar" style="width: {{$perc}}%" aria-valuenow="{{$perc}}" aria-valuemin="0" aria-valuemax="100">{{isset($show_perc) && $perc >=30 && $show_perc != false ? $perc.' %' : ''}}</div>
</div>
