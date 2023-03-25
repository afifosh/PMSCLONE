<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Events\SecuritySettingUpdated;
use App\Http\Controllers\Controller;
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

    public function update(Request $request)
    {
        $this->service->update('security');

        SecuritySettingUpdated::dispatch('security');

        return redirect()->route('admin.setting.security.index')->with(
            'status',
            __('Security settings updated succesfully')
        );
    }

    public function destroy()
    {
        if ($this->service->delete('security')) {
            // 
        }
    }
}
