<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Events\SecuritySettingUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\SecurityRequest;
use App\Services\Core\Setting\General\SettingService;
use Illuminate\Http\Request;

class SecuritySettingController extends Controller
{
    protected $service;

    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('admin.pages.settings.security.index', [
            'setting' => $this->service->getFormattedSettings('security')
        ]);
    }

    public function update(SecurityRequest $request)
    {
        $this->service->update('security');

        SecuritySettingUpdated::dispatch('security');

        return $this->sendRes(
            'Updated security settings',
            [
                'view_data' => view(
                    'admin.pages.settings.security.index',
                )->render()
            ]
        );
    }

    public function destroy()
    {
        if ($this->service->delete('security')) {
            // 
        }
    }
}
