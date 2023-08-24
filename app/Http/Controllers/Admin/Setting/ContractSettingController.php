<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\ContractNotificationRequest;
use App\Http\Requests\Admin\Setting\OnlyOfficeRequest;
use App\Models\Admin;
use App\Services\Core\Setting\SettingService;
use Illuminate\Http\Request;

class ContractSettingController extends Controller
{
  protected $service;

  public function __construct(SettingService $service)
  {
    $this->service = $service;
  }

  public function create()
  {
    $admins = Admin::get();

    return view('admin.pages.settings.contract-notifications.create', ['setting' => $this->service->getFormattedSettings('contract-notifications'), 'admins' => $admins]);
  }

  public function update(ContractNotificationRequest $request)
  {
    $this->service->update('contract-notifications');

    return $this->sendRes('Contract Notification Settings');
  }
}
