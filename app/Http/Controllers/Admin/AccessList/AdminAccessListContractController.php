<?php

namespace App\Http\Controllers\Admin\AccessList;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccessList\Contract\ContractACLStoreRequest;
use App\Models\Admin;
use App\Models\AdminAccessList;
use App\Models\Contract;
use DataTables;
use Illuminate\Support\Facades\DB;

class AdminAccessListContractController extends Controller
{
  public function index($admin_id)
  {
    return DataTables::of(Contract::hasAccessListOfAdmin($admin_id))
      ->addColumn('granted_till', function ($contract) {
        $aclRule = $contract->directACLRules[0] ?? $contract->program->pivotAccessLists[0];
        if (!$aclRule->granted_till) {
          return '<span class="badge bg-label-success">Permanent</span>';
        }

        return date('d M, Y', strtotime($aclRule->granted_till));
      })
      ->addColumn('access_type', function ($contract) {
        if (isset($contract->directACLRules[0])) {
          return '<span class="badge bg-label-danger">Direct</span>';
        }

        return '<span class="badge bg-label-success">Inherited</span>';
      })
      ->addColumn('program', function ($contract) {
        return $contract->program->name ?? '-';
      })
      ->addColumn('status', function ($contract) {
        $aclRule = $contract->directACLRules[0] ?? $contract->program->pivotAccessLists[0];
        if ($aclRule->is_revoked) {
          return '<span class="badge bg-label-danger">Revoked</span>';
        } else if ($aclRule->granted_till && $aclRule->granted_till < date('Y-m-d')) {
          return '<span class="badge bg-label-warning">Expired</span>';
        } else {
          return '<span class="badge bg-label-success">Active</span>';
        }
      })
      ->addColumn('revoke_access', function ($contract) use ($admin_id) {
        $aclRule = $contract->directACLRules[0] ?? $contract->program->pivotAccessLists[0];
        return view('admin.pages.access-lists.contracts.revoke-column', compact('contract', 'admin_id', 'aclRule'));
      })
      ->addColumn('actions', function ($contract)  use ($admin_id) {
        return view('admin.pages.access-lists.contracts.action', compact('contract', 'admin_id'));
      })
      ->rawColumns(['actions', 'status', 'granted_till', 'access_type'])
      ->make(true);
  }

  public function revoke($admin_id, $contract_id)
  {
    DB::beginTransaction();

    try {
      AdminAccessList::updateOrCreate(
        [
          'admin_id' => $admin_id,
          'accessable_id' => $contract_id,
          'accessable_type' => Contract::class
        ],
        [
          'admin_id' => $admin_id,
          'accessable_id' => $contract_id,
          'accessable_type' => Contract::class,
          'is_revoked' => request()->boolean('is_revoked')
        ]
      );

      DB::commit();

      return $this->sendRes('Revoked Successfully', ['event' => 'table_reload', 'table_id' => 'child-table-' . $admin_id]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendErr($e->getMessage());
    }
  }

  public function create(Admin $admin_access_list)
  {
    $data['admin'] = $admin_access_list;
    $data['aclRule'] = new AdminAccessList;

    return $this->sendRes('success', ['view_data' => view('admin.pages.access-lists.contracts.create', $data)->render()]);
  }

  public function store($admin_access_list, ContractACLStoreRequest $request)
  {
    $contract = Contract::findOrFail($request->accessible_id);

    DB::beginTransaction();
    try {
      AdminAccessList::updateOrCreate(
        [
          'admin_id' => $request->admin_id,
          'accessable_id' => $contract->id,
          'accessable_type' => Contract::class
        ],
        [
          'admin_id' => $request->admin_id,
          'accessable_id' => $contract->id,
          'accessable_type' => Contract::class,
          'granted_till' => $request->granted_till,
          'is_revoked' => $request->is_revoked
        ]
      );

      DB::commit();

      return $this->sendRes('Added Successfully', ['event' => 'table_reload', 'table_id' => 'child-table-' . $request->admin_id, 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendErr($e->getMessage());
    }
  }

  public function edit(Admin $admin_access_list, $contract)
  {
    $data['contract'] = Contract::hasAccessListOfAdmin($admin_access_list->id)->findOrFail($contract);
    $data['admin'] = $admin_access_list;
    $data['aclRule'] = $data['contract']->directACLRules[0] ?? $data['contract']->program->pivotAccessLists[0];

    return $this->sendRes('success', ['view_data' => view('admin.pages.access-lists.contracts.create', $data)->render()]);
  }

  public function update($admin_access_list, $contract_id, ContractACLStoreRequest $request)
  {
    $contract = Contract::hasAccessListOfAdmin($request->admin_id)->findOrFail($request->accessible_id);

    DB::beginTransaction();

    try {
      AdminAccessList::updateOrCreate(
        [
          'admin_id' => $request->admin_id,
          'accessable_id' => $contract->id,
          'accessable_type' => Contract::class
        ],
        [
          'admin_id' => $request->admin_id,
          'accessable_id' => $contract->id,
          'accessable_type' => Contract::class,
          'granted_till' => $request->granted_till,
          'is_revoked' => $request->is_revoked
        ]
      );

      DB::commit();

      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'child-table-' . $request->admin_id, 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendErr($e->getMessage());
    }
  }

  public function destroy($admin_access_list, $contract_id)
  {
    DB::beginTransaction();
    try {
      AdminAccessList::where([
        'admin_id' => $admin_access_list,
        'accessable_id' => $contract_id,
        'accessable_type' => Contract::class
      ])->delete();

      DB::commit();

      return $this->sendRes('Contract removed successfully.', ['event' => 'table_reload', 'table_id' => 'child-table-' . $admin_access_list]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendErr($e->getMessage());
    }
  }
}
