<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractSettingController extends Controller
{
  public function index(Contract $contract)
  {
    $contract->load('events');
    return view('admin.pages.contracts.settings.index', compact('contract'));
  }

  public function terminate(Contract $contract, Request $request)
  {
    $request->validate([
      'reason' => 'required|string|max:255',
      'terminate_date' => 'nullable|in:now,custom',
      'custom_date' => 'required_if:terminate_date,custom|nullable|date',
    ]);

    $contract->events()->create([
      'event_type' => 'Terminated',
      'modifications' => [
        'termination_reason' => $request->reason,
        'termination_date' => $request->terminate_date == 'now' ? now() : $request->custom_date,
      ],
      'description' => $request->terminate_date == 'now' ? 'Contract Terminated Immediately' : 'Contract Scheduled For Termination',
      'admin_id' => auth()->id(),
    ]);
    if($request->terminate_date == 'now')
      $contract->update(['status' => 'Terminated']);

    return $this->sendRes('Contract Terminated Successfully', ['event' => 'page_reload']);
  }

  public function pause(Contract $contract, Request $request)
  {
    $request->validate([
      'pause_until' => 'required|in:manual,custom_date,days,weeks,months',
      'custom_date_value' => 'required_if:pause_until,custom_date|date|after:today',
      'pause_days_value' => 'required_if:pause_until,days|integer|min:1',
      'pause_weeks_value' => 'required_if:pause_until,weeks|integer|min:1',
      'pause_months_value' => 'required_if:pause_until,months|integer|min:1',
    ]);
    dd('t');

    $contract->events()->create([
      'event_type' => 'Paused',
      'modifications' => [
        'pause_reason' => $request->reason,
        'pause_date' => $request->pause_date == 'now' ? now() : $request->custom_date,
      ],
      'description' => $request->pause_date == 'now' ? 'Contract Paused Immediately' : 'Contract Scheduled For Pause',
      'admin_id' => auth()->id(),
    ]);

    return $this->sendRes('Contract Paused Successfully', ['event' => 'page_reload']);
  }
}
