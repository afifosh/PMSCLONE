<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Events\DeliverySettingUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\DeliverySettingRequest;
use App\Services\Core\Setting\Delivery\DeliverySettingService;

class DeliverySettingController extends Controller
{
    protected $service;

    public function __construct(DeliverySettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $default = $this->service->getDefaultSettings();

        $deliverySettings = $this->service->getFormattedDeliverySettings(
            [optional($default)->value, 'default_mail_email_name']
        );

        return view('admin.pages.settings.delivery.index', ['settings' => $deliverySettings]);
    }

    public function update(DeliverySettingRequest $request)
    {
        $context = $request->get('provider');

        foreach ($request->only('from_name', 'from_email') as $key => $value) {
            $this->service->update($key, $value, 'default_mail_email_name');
        }

        foreach ($request->except('_token', '_method', 'from_name', 'from_email') as $key => $value) {
            $this->service->update($key, $value, $context);
        }

        $this->service->setDefaultSettings('default_mail', $context);

        DeliverySettingUpdated::dispatch();
    }
}
