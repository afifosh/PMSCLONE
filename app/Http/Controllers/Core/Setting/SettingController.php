<?php


namespace App\Http\Controllers\Core\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Setting\SettingRequest;
use App\Notifications\Core\Settings\SettingsNotification;
use App\Services\Core\Setting\SettingService;
use Illuminate\Http\Response;

class SettingController extends Controller
{

  public function __construct(SettingService $setting)
  {
    $this->service = $setting;
  }

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    return view('admin.pages.settings.general.index', [
      'setting' => $this->service->getFormattedSettings()
    ]);
  }


  /**
   * @param SettingRequest $request
   * @return array
   */
  public function update(SettingRequest $request)
  {
    $this->service->update();

    return $this->sendRes('Updated general settings');
  }
}
