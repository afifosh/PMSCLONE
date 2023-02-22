<li onclick="location.href='{{$notification->data['data']['action_url']}}';" class="list-group-item list-group-item-action dropdown-notifications-item">
    <div class="d-flex">
        <div class="flex-shrink-0 me-3">
            <div class="avatar">
                <img src="{{$notification->data['data']['image']}}"
                    alt="" class="h-auto rounded-circle">
            </div>
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-1">{{$notification->data['title']}} {{ auth()->id() == $notification->data['data']['shared_with_id'] ? 'You.' : $notification->data['data']['shared_with_name']}}</h6>
            <p class="mb-0">{{$notification->data['data']['description']. ' with '}} {{ auth()->id() == $notification->data['data']['shared_with_id'] ? 'You.' : $notification->data['data']['shared_with_name']}}</p>
            <small class="text-muted">{{$notification->created_at->diffForHumans()}}</small>
        </div>
        <div class="flex-shrink-0 dropdown-notifications-actions">
            <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
        </div>
    </div>
</li>
