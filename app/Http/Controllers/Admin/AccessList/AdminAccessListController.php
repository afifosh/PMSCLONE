<?php

namespace App\Http\Controllers\Admin\AccessList;

use App\DataTables\Admin\AccessList\AdminAccessListsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminAccessList;
use Illuminate\Http\Request;

class AdminAccessListController extends Controller
{
  public function index(AdminAccessListsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.access-lists.index');
    // view('admin.pages.access-lists.index');
  }

  public function create()
  {
    $data['users'] = Admin::whereDoesntHave('accessiblePrograms')->get();
    $data['adminAccessList'] = new AdminAccessList;

    return $this->sendRes('success', [
      'view_data' => view('admin.pages.access-lists.create', $data)->render(),
      'JsMethods' => ['initACLCreateTreeSelect'],
      // 'JsMethodsParams' => []
    ]);
  }

  public function edit(AdminAccessList $adminAccessList)
  {
    $data['adminAccessList'] = $adminAccessList;
    $data['users'] = Admin::whereDoesntHave('accessiblePrograms')->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.access-lists.create', $data)->render()]);
  }
}
