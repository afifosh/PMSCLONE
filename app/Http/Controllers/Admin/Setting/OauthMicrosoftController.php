<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\OauthMicrosoftRequest;
use App\Services\Core\Setting\SettingService;

class OauthMicrosoftController extends Controller
{

  protected $service;

  public function __construct(SettingService $service)
  {
    $this->service = $service;
  }

  public function create()
  {
    return view('admin.pages.settings.oauth-microsoft.index', ['setting' => $this->service->getFormattedSettings('oauth-microsoft')]);
  }

  public function store(OauthMicrosoftRequest $request)
  {
    $this->service->update('oauth-microsoft');

    return $this->sendRes('Updated Microsoft Oauth settings');
  }
}
