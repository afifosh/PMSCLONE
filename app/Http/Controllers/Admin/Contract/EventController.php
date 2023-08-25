<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\EventsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Contract;
use App\Models\ContractEvent;
use Illuminate\Http\Request;

class EventController extends Controller
{
  public function index(Contract $contract, EventsDataTable $dataTable)
  {
    $dataTable->contract = $contract;
    $contract->load('notifiableUsers');
    $data['contract'] = $contract;
    $data['summary'] = $contract->events()->selectRaw('event_type, count(*) as total')->groupBy('event_type')->get();

    $data['actioners'] = Admin::whereHas('contractEvents', function ($query) use ($contract) {
      $query->where('contract_id', $contract->id);
    })->pluck('email', 'id');

    $data['event_types'] = ContractEvent::getPossibleEnumValues('event_type');

    return $dataTable->render('admin.pages.contracts.events.index', $data);
    // view('admin.pages.contracts.events.index');
  }
}
