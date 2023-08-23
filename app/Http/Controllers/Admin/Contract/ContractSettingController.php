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
      'description' => $request->terminate_date == 'now' ? 'Contract Terminated' : 'Contract Scheduled For Termination',
      'admin_id' => auth()->id(),
    ]);
    if($request->terminate_date == 'now')
      $contract->update(['status' => 'Terminated']);

    return $this->sendRes('Contract Terminated Successfully', ['event' => 'page_reload']);
  }

  public function pause(Contract $contract, Request $request)
  {
    if($contract->status == 'Paused' || $contract->status == 'Terminated')
      return $this->sendError('Contract Already '. $contract->status);
    $request->validate([
      'pause_until' => 'required|in:manual,custom_date,custom_unit',
      'custom_date_value' => 'nullable|required_if:pause_until,custom_date|date|after:today',
      'custom_unit' => 'required_if:pause_until,custom_unit|in:Days,Weeks,Months',
      'pause_for' => 'nullable|required_if:pause_until,custom_unit|numeric|min:1',
    ],[
      'pause_for.required_if' => 'This field is required',
    ]);

    if($request->pause_until == 'manual'){
      $description = 'Contract Paused Until Manual Resume';
      $modifications = ['pause_until' => 'manual'];
    }else if($request->pause_until == 'custom_date'){
      $description = 'Contract Paused Until '.$request->custom_date_value;
      $modifications = ['pause_until' => $request->custom_date_value, 'pause_date' => now()];
    }else if($request->pause_until == 'custom_unit'){
      $description = 'Contract Paused For '.$request->pause_for.' '.$request->custom_unit;
      $modifications = ['pause_until' => now()->{'add'.$request->custom_unit}($request->pause_for), 'pause_date' => now()];
    }

    $contract->events()->create([
      'event_type' => 'Paused',
      'modifications' => $modifications,
      'description' => $description,
      'admin_id' => auth()->id(),
    ]);

    $contract->update(['status' => 'Paused']);

    return $this->sendRes('Contract Paused Successfully', ['event' => 'page_reload']);
  }
}
