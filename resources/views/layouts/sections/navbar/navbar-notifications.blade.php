@foreach($notifications as $notification)
@if ($notification->type == 'App\Notifications\Auth\LoginNotification\NewDevice' || $notification->type == 'App\Notifications\Auth\LoginNotification\NewLocation')
<li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
  <div class="d-flex">
    <div class="flex-shrink-0 me-3">
      <div class="avatar">
        <span class="avatar-initial rounded-circle bg-label-warning"><i class="ti ti-alert-triangle"></i></span>
      </div>
    </div>
    <div class="flex-grow-1">
      <h6 class="mb-1">{{ __('Suspecious actvity on your account') }}</h6>
      <p class="mb-0">
        {{ __('Recently we have detected unusual activity your account.') }}
        <br>
        <br> <b>@lang('IP address:')</b> {{ $notification->data['ipAddress'] }}
        <br> <b>@lang('City:')</b> {{ $notification->data['location']['city'] }}
        <br> <b>@lang('Country:')</b> {{ $notification->data['location']['country'] }}
      </p>
    </div>
    <div class="flex-shrink-0 dropdown-notifications-actions">
      <a href="javascript:void(0)" class="dropdown-notifications-read">test<span class="badge badge-dot"></span></a>
      <a href="javascript:void(0)" class="dropdown-notifications-archive">test2<span class="ti ti-x"></span></a>
    </div>
  </div>
</li>
@else
@isset($notification->data['data']['view'])
@include($notification->data['data']['view'], ['notification' => $notification])
@endisset
@endif

@endforeach
