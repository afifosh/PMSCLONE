<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\OauthGoogleRequest;
use App\Services\Core\Setting\SettingService;

class OauthGoogleController extends Controller
{

  protected $service;

  public function __construct(SettingService $service)
  {
    $this->service = $service;
  }

  public function create()
  {
    return view('admin.pages.settings.oauth-google.index', ['setting' => $this->service->getFormattedSettings('oauth-google')]);
  }

  public function store(OauthGoogleRequest $request)
  {
    $this->service->update('oauth-google');

    return $this->sendRes('Updated Google Oauth settings');
  }
}
