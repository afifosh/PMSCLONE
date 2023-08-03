<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\BroadcastRequest;
use App\Services\Core\Setting\DeliverySettingService;
use App\Services\Core\Setting\SettingService;

class BroadcastSettingController extends Controller
{
  protected $service;

  public function __construct(SettingService $service)
  {
    $this->service = $service;
  }

  public function index()
  {
    return view('admin.pages.settings.broadcast.index', ['setting' => $this->service->getFormattedSettings('broadcast_driver')]);
  }

  public function update(BroadcastRequest $request)
  {
    $this->service->update('broadcast_driver');

    return $this->sendRes('Updated broadcast settings', ['view_data' => view('admin.pages.settings.broadcast.index')->render()]);
  }
}
