<div>
    @if($row->type === 'App\\Notifications\\Auth\\NewLocation')
    <span class="icon">
        <i class="fa fa-earth-europe"></i>
    </span>
    <span class="mx-2 text-uppercase fw-bold">
        @lang('New Location')
    </span>

    @elseif($row->type === 'App\\Notifications\\Auth\\NewDevice')
    <span class="icon">
        <i class="fa fa-desktop"></i>
    </span>
    <span class="mx-2 text-uppercase fw-bold">
        @lang('New Device')
    </span>

    @elseif($row->type === 'App\\Notifications\\Auth\\FailedLogin')
    <span class="icon">
        <i class="fa fa-circle-exclamation"></i>
    </span>
    <span class="mx-2 text-uppercase fw-bold">
        @lang('Failed Login')
    </span>
    @endif
</div>