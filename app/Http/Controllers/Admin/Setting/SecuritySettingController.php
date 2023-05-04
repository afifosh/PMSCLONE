<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\SecurityRequest;
use App\Services\Core\Setting\SettingService;
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

        return $this->sendRes('Updated security settings', ['event' => 'page_reload']);
    }

    public function destroy()
    {
        if ($this->service->delete('security')) {
            //
        }
    }
}
