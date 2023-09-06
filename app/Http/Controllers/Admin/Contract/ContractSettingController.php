<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\NotifiableUsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Contract;
use App\Notifications\Admin\Contract\ContractTerminationNotification;
use App\Services\Core\Setting\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContractSettingController extends Controller
{
  public function index(Contract $contract, NotifiableUsersDataTable $dataTable)
  {
    $contract->load('events');
    $dataTable->contract = $contract;

    return $dataTable->render('admin.pages.contracts.settings.index', compact('contract'));
    // return view('admin.pages.contracts.settings.index', compact('contract'));
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
    if($request->terminate_date == 'now'){
      $contract->update(['status' => 'Terminated']);
      $this->sendTerminateNotification($contract);
    }


    return $this->sendRes('Contract Terminated Successfully', ['event' => 'page_reload']);
  }

  public function undoTerminate(Contract $contract, Request $request)
  {
    $contract->load('events');
    if($contract->status != 'Terminated' && $contract->events->where('event_type', 'Terminated')->whereNull('applied_at')->count() == 0)
      return $this->sendError('Contract Not Terminated');

    $contract->events()->create([
      'event_type' => 'Undo Terminate',
      'modifications' => [
        'termination_reason' => $contract->termination_reason,
        'termination_date' => $contract->termination_date,
      ],
      'description' => 'Contract Termination Cancelled',
      'admin_id' => auth()->id(),
    ]);

    $contract->update(['status' => 'Active']);

    // mark the termination event as applied
    $contract->events()->where('event_type', 'Terminated')->whereNull('applied_at')->update(['applied_at' => now()]);

    return $this->sendRes('Contract Termination Cancelled', ['event' => 'page_reload']);
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
      $modifications = ['pause_until' => 'manual', 'pause_date' => now()];
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

  public function resume(Contract $contract, Request $request)
  {
    if($contract->status != 'Paused')
      return $this->sendError('Contract Not Paused');

    $contract->resume();

    return $this->sendRes('Contract Resumed Successfully', ['event' => 'page_reload']);
  }

  public function sendTerminateNotification($contract, $isImmediate = true)
  {
    $admins = $contract->notifiableUsers;
    $config = (new SettingService())->getFormattedSettings('contract-notifications');

    if (isset($config['enable_notifications']) && $config['enable_notifications'] == 1) {
      $adminsGlobal = Admin::whereIn('id', explode(',', $config['emails']))->get();
      $admins = $admins->merge($adminsGlobal);

      Notification::send($admins, new ContractTerminationNotification($contract, $isImmediate));
    }
  }
}
