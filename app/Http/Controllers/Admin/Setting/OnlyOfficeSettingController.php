<?php

namespace App\Http\Controllers\Admin\Setting;


use App\Events\OnlyOfficeSettingUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\OnlyOfficeRequest;
use App\Services\Core\Setting\General\SettingService;
use Illuminate\Http\Request;

class OnlyOfficeSettingController extends Controller
{
    protected $service;

    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        return view(
            'admin.pages.settings.onlyoffice.index',
            [
                'setting' => $this->service->getFormattedSettings('onlyoffice')
            ]
        );
    }

    public function update(OnlyOfficeRequest $request)
    {
        $this->service->update('onlyoffice');

        OnlyOfficeSettingUpdated::dispatch('onlyoffice');

        return $this->sendRes(
            'Updated onlyoffice settings',
            [
                'view_data' => view(
                    'admin.pages.settings.onlyoffice.index',
                )->render()
            ]
        );
    }
}
