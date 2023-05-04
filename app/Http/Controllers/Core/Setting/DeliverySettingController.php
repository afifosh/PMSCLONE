<?php

namespace App\Http\Controllers\Core\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Setting\DeliverySettingRequest as Request;
use App\Mail\TestMail;
use App\Services\Core\Setting\DeliverySettingService;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Mail;


class DeliverySettingController extends Controller
{
  public function __construct(DeliverySettingService $service)
  {
    $this->service = $service;
  }

  public function index()
  {
    $default = $this->service->getDefaultSettings();

    $deliverySettings = $this->service->getFormattedDeliverySettings(
      [optional($default)->value, 'default_mail_email_name']
    );

    return view('admin.pages.settings.delivery.index', ['settings' => $deliverySettings]);
  }

  public function update(Request $request)
  {
    $context = $request->get('provider');

    foreach ($request->only('from_name', 'from_email') as $key => $value) {
      $this->service
        ->update($key, $value, 'default_mail_email_name');
    }

    foreach ($request->except('allowed_resource', 'from_name', 'from_email') as $key => $value) {
      $this->service
        ->update($key, $value, $context);
    }

    $this->service->setDefaultSettings('default_mail', $context);

    // return updated_responses('delivery_settings');
    return $this->sendRes('Updated delivery settings');
  }

  public function show(Request $request)
  {
    $request->validate(['provider' => 'required']);

    return $this->service->getFormattedDeliverySettings(
      [$request->provider, 'default_mail_email_name']
    );
  }

  public function sendTestEmail(HttpRequest $request)
  {
    if ($request->method() == 'GET') {
      return $this->sendRes('', ['view_data' => view('admin.pages.settings.delivery.send_test_email')->render()]);
    }

    $request->validate([
      'email' => 'required|email',
      'subject' => 'required|string|max:255',
      'message' => 'required|string|max:255',
    ]);

    try {
      Mail::to($request->email)
        ->send(new TestMail($request->subject, $request->message));
      return $this->sendRes('Test email sent successfully', ['']);
    } catch (\Exception $exception) {
      return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => $exception->getMessage()]);
    }
  }
}
