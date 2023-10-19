<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ArtworksDataTable;
use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Medium;
use App\Repositories\FileUploadRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\ProfileUpdateRequest;
use Throwable;

class ArtworkController extends Controller
{

  function __construct()
  {
    // $this->middleware('permission:read company|create company|update company|delete company', ['only' => ['index', 'show', 'showUsers', 'showInvitations']]);
    // $this->middleware('permission:create company', ['only' => ['create', 'store']]);
    // $this->middleware('permission:update company', ['only' => ['edit', 'update']]);
    // $this->middleware('permission:delete company', ['only' => ['destroy']]);
  }

  public function index(ArtworksDataTable $datatable)
  {
    return $datatable->render('admin.pages.artwork.index');
  }

  public function create()
  {
    $data['artwork'] = new Artwork();
    $data['$mediums'] = ['' => 'Select Medium'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.artwork.edit', $data)->render(), 'JsMethods' => ['initIntlTel']]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'year' => ['required', 'integer'],
        'medium' => ['required', 'string', 'max:255'],
        'dimension' => ['required', 'string', 'max:255'],
    ]);


    if (Artwork::create($att + ['added_by' => auth()->id()])) {
      return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Artwork::DT_ID, 'close' => 'globalModal']);
    }
  }

  public function show(Artwork $artwork)
  {
    $data['artwork'] = $artwork;
    $data['mediums'] = $artwork->medium_id ? Medium::where('id', $artwork->medium_id)->pluck('name', 'id')->prepend('Select Medium', '') : ['' => 'Select Medium'];

    return view('admin.pages.artwork.show-profile', $data);
  }


  public function edit(Artwork $artwork)
  {
    $data['artwork'] = $artwork;
    $data['mediums'] = $artwork->medium_id ? Medium::where('id', $artwork->medium_id)->pluck('name', 'id')->prepend('Select Medium', '') : ['' => 'Select Medium'];

    return view('admin.pages.artwork.edit', $data);
  }

  public function update(Request $request, $id)
  {
    // Retrieve the Artwork from the database by ID
    $artwork = Artwork::find($id);
    
    if (!$artwork) {
        // Handle the case where the Artwork with the given ID is not found
        return response()->json(['message' => 'Artwork not found'], 404);
    }    

    $att = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'year' => ['required', 'integer'],
        'medium' => ['required', 'string', 'max:255'],
        'dimension' => ['required', 'string', 'max:255'],
    ]);

    $artwork->update($att);

    $data['artwork'] = $artwork;
    $data['mediums'] = $artwork->medium_id ? Medium::where('id', $artwork->medium_id)->pluck('name', 'id')->prepend('Select Medium', '') : ['' => 'Select Medium'];
        return $this->sendRes('Updated Successfully', [
          'view_data' => view('admin.pages.artwork.edit', $data)->render(),
          'JsMethods' => ['initIntlTel'],
      ]);

    // }
  }




  public function destroy(Artwork $artwork)
  {
    try {
      if ($artwork->delete()) {
        return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => Artwork::DT_ID]);
      }
      return $this->sendError('Something Went Wrong');
    } catch (Throwable $e) {
      return $this->sendError('Server Error');
    }
  }




}
