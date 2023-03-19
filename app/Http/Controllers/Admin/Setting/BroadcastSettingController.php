<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\BroadcastRequest;
use App\Services\Core\Setting\Delivery\DeliverySettingService;

class BroadcastSettingController extends Controller
{
    protected $service;

    public function __construct(DeliverySettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $default = $this->service->getDefaultSettings($key = 'default_broadcast');

        return view(
            'admin.pages.settings.broadcast.index',
            [
                'setting' => $this->service
                    ->getFormattedDeliverySettings([optional($default)->value, 'default_broadcast_driver_name'])
            ]
        );
    }

    public function update(BroadcastRequest $request)
    {
        $context = $request->get('broadcast_driver');

        foreach ($request->only('pusher_app_id', 'pusher_app_key', 'pusher_app_secret', 'pusher_app_cluster') as $key => $value) {
            $this->service->update($key, $value, 'default_broadcast_driver_name');
        }

        foreach ($request->except('_token', '_method', 'allowed_resource', 'pusher_app_id', 'pusher_app_key', 'pusher_app_secret', 'pusher_app_cluster') as $key => $value) {
            $this->service->update($key, $value, $context);
        }

        $this->service->setDefaultSettings('default_broadcast', $context, $context = 'broadcast');
    }
}
