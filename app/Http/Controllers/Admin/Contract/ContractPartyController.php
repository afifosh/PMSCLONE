<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\ContractPartiesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\ContractPartyRequest;
use App\Models\Contract;
use App\Models\ContractParty;
use Illuminate\Http\Request;

class ContractPartyController extends Controller
{
  public function index(Contract $contract, ContractPartiesDataTable $dataTable)
  {
    $dataTable->contract = $contract;
    $data['contract'] = $contract;
    return $dataTable->render('admin.pages.contracts.contract-parties.index', $data);
    // view('admin.pages.contracts.contract-parties.index');
  }

  public function create(Contract $contract)
  {
    $data['contract'] = $contract;
    $data['contractParty'] = new ContractParty();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.contract-parties.create', $data)->render()]);
  }

  public function store(ContractPartyRequest $request, Contract $contract)
  {
    $contractParty = new ContractParty();
    $contractParty->contract_id = $contract->id;
    $contractParty->contract_party_type = $request->contract_party_type;
    $contractParty->contract_party_id = $request->contract_party_id;
    $contractParty->save();

    return $this->sendRes('Contract Party Added Successfully', ['event' => 'table_reload', 'table_id' => 'contract-parties-table', 'close' => 'globalModal']);
  }

  public function edit(Contract $contract, ContractParty $contractParty)
  {
    $data['contract'] = $contract;
    $data['contractParty'] = $contractParty;
    $data['partyType'] = $contractParty->contract_party_type == 'App\Models\PartnerCompany' ? 'PartnerCompany' : ($contractParty->party ?  ($contractParty->party->type == 'Company' ? 'Company' : 'Client') : 'Select Party Type');
    $data['contractParties'] = [$contractParty->contract_party_id => $contractParty->party ? $contractParty->party->name : 'Select Party'];

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.contract-parties.create', $data)->render()]);
  }

  public function update(Contract $contract, ContractParty $contractParty, ContractPartyRequest $request)
  {
    $contractParty->contract_party_type = $request->contract_party_type;
    $contractParty->contract_party_id = $request->contract_party_id;
    $contractParty->save();

    return $this->sendRes('Contract Party Updated Successfully', ['event' => 'table_reload', 'table_id' => 'contract-parties-table', 'close' => 'globalModal']);
  }

  public function destroy(Contract $contract, ContractParty $contractParty)
  {
    $contractParty->delete();

    return $this->sendRes('Contract Party Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'contract-parties-table']);
  }
}
