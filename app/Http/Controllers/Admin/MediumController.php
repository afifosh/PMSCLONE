<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\MediumsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Medium;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Throwable;

class MediumController extends Controller
{
  function __construct()
  {
    // $this->middleware('permission:read company|create company|update company|delete company', ['only' => ['index', 'show', 'showUsers', 'showInvitations']]);
    // $this->middleware('permission:create company', ['only' => ['create', 'store']]);
    // $this->middleware('permission:update company', ['only' => ['edit', 'update']]);
    // $this->middleware('permission:delete company', ['only' => ['destroy']]);
  }

    
  public function index(MediumsDataTable $datatable)
  {
    return $datatable->render('admin.pages.medium.index');
  }

  public function create()
  {
    $data['Medium'] = new Medium();
    return $this->sendRes('success', ['view_data' => view('admin.pages.medium.edit', $data)->render(), 'JsMethods' => ['initIntlTel']]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
    ]);


    if (Medium::create($att + ['added_by' => auth()->id()])) {
      return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Medium::DT_ID, 'close' => 'globalModal']);
    }
  }

  public function show(Medium $medium)
  {
    $data['medium'] = $medium;

    return view('admin.pages.medium.show-profile', $data);
  }


  public function edit(Medium $medium)
  {
    $data['medium'] = $medium;

    return view('admin.pages.medium.edit', $data);
  }

  public function update(Request $request, $id)
  {
    // Retrieve the Medium from the database by ID
    $medium = Medium::find($id);
    
    if (!$medium) {
        // Handle the case where the Medium with the given ID is not found
        return response()->json(['message' => 'Medium not found'], 404);
    }    

    $att = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
    ]);

    $medium->update($att);

    $data['medium'] = $medium;
 
        return $this->sendRes('Updated Successfully', [
          'view_data' => view('admin.pages.medium.edit', $data)->render(),
          'JsMethods' => ['initIntlTel'],
      ]);

    // }
  }


  public function destroy(Medium $medium)
  {
    try {
      if ($medium->delete()) {
        return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => Medium::DT_ID]);
      }
      return $this->sendError('Something Went Wrong');
    } catch (Throwable $e) {
      return $this->sendError('Server Error');
    }
  }



}

