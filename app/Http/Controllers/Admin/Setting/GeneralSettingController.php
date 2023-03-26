<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Events\GeneralSettingUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\SettingRequest;
use App\Services\Core\Setting\General\SettingService;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    protected $service;

    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('admin.pages.settings.general.index', [
            'setting' => $this->service->getFormattedSettings('app')
        ]);
    }

    public function update(SettingRequest $requst)
    {
        $this->service->update();

        GeneralSettingUpdated::dispatch();

        return $this->sendRes(
            'Updated general settings',
            [
                'view_data' => view(
                    'admin.pages.settings.general.index',
                )->render()
            ]
        );

    }
}
