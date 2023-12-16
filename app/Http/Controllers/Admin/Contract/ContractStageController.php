<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\StagesDataTable;
use App\Events\Admin\Contract\ContractUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\Stage\StageStoreRequest;
use App\Http\Requests\Admin\Contract\Stage\StageUpdateRequest;
use App\Models\Contract;
use App\Models\ContractStage;

class ContractStageController extends Controller
{
  public function __construct()
  {
    // check if stage belongs to contract
    $this->middleware('verifyContractNotTempered:stage,contract_id')->only(['edit', 'update', 'destroy']);

    $this->middleware('permission:read contract')->only(['index']);
    $this->middleware('permission:create contract')->only(['create', 'store']);
    $this->middleware('permission:update contract')->only(['edit', 'update']);
    $this->middleware('permission:delete contract')->only(['destroy']);
  }

  public function index(Contract $contract, StagesDataTable $dataTable)
  {
    $dataTable->contract = $contract;

    $page = 'Project';
    if (request()->route()->getName() == 'admin.contracts.stages.index') {
      $page = 'Contract';
      $contract->load('notifiableUsers');
    }

    return $dataTable->render('admin.pages.contracts.stages.index', compact('contract', 'page'));
    // return view('admin.pages.contracts.stages.index')
  }

  public function create(Contract $contract)
  {
    $stage = new ContractStage();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.stages.create', compact('contract', 'stage'))->render()]);
  }

  public function store(Contract $contract, StageStoreRequest $request)
  {
    $contract->stages()->create($request->only(['name']));
    broadcast(new ContractUpdated($contract, 'stages'))->toOthers();

    return $this->sendRes(__('Stage Created Successfully'), ['event' => 'functionCall', 'function' => 'reloadDataTables', 'close' => 'globalModal']);
  }

  public function edit(Contract $contract, ContractStage $stage)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.stages.create', compact('contract', 'stage'))->render()]);
  }

  public function update(StageUpdateRequest $request, Contract $contract, ContractStage $stage)
  {
    $stage->update($request->only(['name']));
    broadcast(new ContractUpdated($contract, 'stages'))->toOthers();

    return $this->sendRes(__('Stage Updated Successfully'), ['event' => 'functionCall', 'function' => 'reloadDataTables', 'close' => 'globalModal']);
  }

  public function destroy(Contract $contract, ContractStage $stage)
  {
    $stage->phases()->delete();
    $stage->delete();
    broadcast(new ContractUpdated($contract, 'stages'))->toOthers();

    return $this->sendRes(__('Stage Deleted Successfully'), ['event' => 'functionCall', 'function' => 'reloadDataTables']);
  }
}
