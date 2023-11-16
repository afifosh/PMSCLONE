<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ArtworksDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Artwork\ArtworkStoreRequest;
use App\Models\Artwork;
use App\Models\Medium;
use App\Repositories\FileUploadRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Location;
use App\Enums\LengthUnit;
use App\Enums\WeightUnit;
use Illuminate\Support\Facades\DB;
use Throwable;

class ArtworkController extends Controller
{
  public function index(ArtworksDataTable $datatable)
  {
    return $datatable->render('admin.pages.artwork.index');
  }

  public function create()
  {
    $data['artwork'] = new Artwork();
    $data['weight_unit'] = WeightUnit::asSelectArray() ?? ['' => 'Select Unit'];
    $data['width_unit'] = LengthUnit::asSelectArray() ?? ['' => 'Select Unit'];
    $data['height_unit'] = LengthUnit::asSelectArray() ?? ['' => 'Select Unit'];
    $data['depth_unit'] = LengthUnit::asSelectArray() ?? ['' => 'Select Unit'];

    return $this->sendRes('success', ['view_data' => view('admin.pages.artwork.create', $data)->render()]);
  }


  
  public function store(ArtworkStoreRequest $request, FileUploadRepository $file_repo)
  {
      DB::beginTransaction(); // Start the transaction
  
      try {
          // Find or create the medium
          $medium = Medium::where('id', $request->medium_id)->firstOrCreate(['name' => $request->medium_id]);
  
          // Check for featured_image in the request
          if ($request->hasFile('featured_image')) {

              // Assuming you have a service or method to handle the file upload
              // Update the path according to your application structure
              $path = Artwork::ARTWORK_PATH; // Use the ARTWORK_PATH constant

              $featuredImage = $path . '/' . $file_repo->addAttachment($request->file('featured_image'), $path);
  
              // Prepare the data for creating the Artwork
              $artworkData = [
                  'medium_id' => $medium->id,
                  'featured_image' => $featuredImage,
                  // other validated data from the request
              ] + $request->validated();
  
              // Create the Artwork record
              $artwork = Artwork::create($artworkData);
  
              DB::commit(); // Commit the transaction
  
              // Return a success response, for example:
              return response()->json(['success' => 'Artwork created successfully.', 'artwork' => $artwork]);
          } else {
              DB::rollBack(); // Rollback the transaction
              // Handle the case where the featured_image is not uploaded
              return response()->json(['error' => 'Featured image is required.'], 422);
          }
      } catch (\Exception $e) {
          DB::rollBack(); // Rollback the transaction on error
          // Handle exceptions
          return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
      }
  }

  
  public function storeold(ArtworkStoreRequest $request)
  {
    $medium = Medium::where('id', $request->medium_id)->firstOrCreate(['name' => $request->medium_id]);
    $artwork = Artwork::create(['medium_id' => $medium->id] + $request->validated());


    // create location if warehouse is not selected
    // if(!$request->warehouse_id){
    //   $location = Location::create([
    //     // values...
    //   ]);
    // }

    // add location for artwork
    // $artwork->locations()->create(['location_id' => $location->id ?? null] + $request->validated());

    // $artwork->locations()->attach([
    //   $request->location_id => [
    //     'contract_id' => $request->contract_id,
    //     'added_by' => $request->added_by,
    //     'added_till' => $request->added_till
    //   ]
    // ]);

    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Artwork::DT_ID, 'close' => 'globalModal']);
  }

  public function show(Artwork $artwork)
  {
    $data['artwork'] = $artwork;
    $data['mediums'] = $artwork->medium_id ? Medium::where('id', $artwork->medium_id)->pluck('name', 'id')->prepend('Select Medium', '') : ['' => 'Select Medium'];

    return view('admin.pages.artwork.show-profile', $data);
  }


  public function edit(Artwork $artwork)
  {
    $artwork->load('latestLocation.location', 'latestLocation.contract');
    $data['artwork'] = $artwork;
    $data['mediums'] = $artwork->medium_id ? Medium::where('id', $artwork->medium_id)->pluck('name', 'id')->prepend('Select Medium', '') : ['' => 'Select Medium'];
    $data['programs'] = [$artwork->program_id => $artwork->program->name ?? ''];
    $data['locations'] = [$artwork->latestLocation->location_id ?? '' => $artwork->latestLocation->location->name ?? 'Select Location'];
    $data['weight_unit'] = WeightUnit::asSelectArray() ?? ['' => 'Select Unit'];
    $data['width_unit'] = LengthUnit::asSelectArray() ?? ['' => 'Select Unit'];
    $data['height_unit'] = LengthUnit::asSelectArray() ?? ['' => 'Select Unit'];
    $data['depth_unit'] = LengthUnit::asSelectArray() ?? ['' => 'Select Unit'];


    return $this->sendRes('success', ['view_data' => view('admin.pages.artwork.create', $data)->render()]);
  }

  public function update(Artwork $artwork, ArtworkStoreRequest $request)
  {
    $artwork->update($request->validated());


    return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => Artwork::DT_ID, 'close' => 'globalModal']);
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
