<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\ArtistsDataTable;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Artist;
use App\Models\Country;
use App\Models\State;
use App\Repositories\FileUploadRepository;
use App\Support\Timezonelist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\ProfileUpdateRequest;
use Throwable;

class ArtistController extends Controller
{

  function __construct()
  {
    // $this->middleware('permission:read company|create company|update company|delete company', ['only' => ['index', 'show', 'showUsers', 'showInvitations']]);
    // $this->middleware('permission:create company', ['only' => ['create', 'store']]);
    // $this->middleware('permission:update company', ['only' => ['edit', 'update']]);
    // $this->middleware('permission:delete company', ['only' => ['destroy']]);
  }

  public function index(ArtistsDataTable $datatable)
  {
    return $datatable->render('admin.pages.artist.index');
    return view('admin.pages.artist.index');
  }

  public function create()
  {
    $data['artist'] = new Artist();
    $data['countries'] = ['' => 'Select Country'];
    $data['states'] = ['' => 'Select State'];
    $data['cities'] = ['' => 'Select City'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.artist.edit', $data)->render(), 'JsMethods' => ['initIntlTel']]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
      'website' => ['nullable', 'string', 'max:255', 'unique:companies,website'],
      'email' => ['nullable', 'string', 'max:255', 'unique:companies,email'],
      // 'status' => 'required',
      'type' => 'required|in:Company,Person',
      'phone' => 'nullable|phone',
      'phone_country' => 'required_with:phone',
      'address' => 'nullable|string|max:255',
      'city_id' => 'nullable|exists:cities,id',
      'state_id' => 'nullable|exists:states,id',
      'zip' => 'nullable|string|max:255',
      'country_id' => 'nullable|exists:countries,id',
      'vat_number' => 'nullable|string|max:255',
      'gst_number' => 'nullable|string|max:255',
    ]);


    if (Artist::create($att + ['added_by' => auth()->id()])) {
      return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Artist::DT_ID, 'close' => 'globalModal']);
    }
  }

  public function show(Artist $artist)
  {
    $data['artist'] = $artist;
    $data['timezones'] = $this->timezones();
    $data['languages'] = $this->languages();
    $data['currencies'] = $this->currencies();
    $data['countries'] = $artist->country_id ? Country::where('id', $artist->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
    $data['states'] = $artist->state_id ? State::where('id', $artist->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
    $data['cities'] = $artist->city_id ? City::where('id', $artist->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];

    return view('admin.pages.artist.show-profile', $data);
  }


  public function edit(Artist $artist)
  {
    $data['artist'] = $artist;
    $data['timezones'] = $this->timezones();
    $data['languages'] = $this->languages();
    $data['currencies'] = $this->currencies();
    $data['countries'] = $artist->country_id ? Country::where('id', $artist->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
    $data['states'] = $artist->state_id ? State::where('id', $artist->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
    $data['cities'] = $artist->city_id ? City::where('id', $artist->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];

    return view('admin.pages.artist.edit', $data);
    //return $this->sendRes('success', ['view_data' => view('admin.pages.artist.edit', $data)->render(), 'JsMethods' => ['initIntlTel']]);
  }

  public function update(Request $request, $id)
  {
    // Retrieve the artist from the database by ID
    $artist = Artist::find($id);
    
    if (!$artist) {
        // Handle the case where the artist with the given ID is not found
        return response()->json(['message' => 'Artist not found'], 404);
    }    

    $att = $request->validate([
      // 'profile' => 'sometimes|mimetypes:image/*',
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'gender' => 'nullable|in:Male,Female,Other',
      'phone' => 'phone',
      'phone_country' => 'required_with:phone',
      'address' => 'nullable|string|max:255',
      'zip_code' => 'nullable|string|max:8',
      'country_id' => 'nullable|exists:countries,id',
      'state_id' => 'nullable|exists:states,id',
      'city_id' => 'nullable|exists:cities,id',
      'language' => 'nullable|string|max:255',
      'timezone' => 'nullable|string|max:255',
      'currency' => 'nullable|string|max:255',
    ]);

    $artist->update($att);

    $data['artist'] = $artist;
    $data['timezones'] = $this->timezones();
    $data['languages'] = $this->languages();
    $data['currencies'] = $this->currencies();
    $data['countries'] = $artist->country_id ? Country::where('id', $artist->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
    $data['states'] = $artist->state_id ? State::where('id', $artist->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
    $data['cities'] = $artist->city_id ? City::where('id', $artist->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];


        return $this->sendRes('Updated Successfully', [
          'view_data' => view('admin.pages.artist.edit', $data)->render(),
          'JsMethods' => ['initIntlTel'],
      ]);

    // }
  }

  // public function updatesss(Request $request, Artist $artist)
  // {
  //   $att = $request->validate([
  //     'name' => 'required|string|max:255|unique:programs,name,'.$program->id.',id',
  //     'program_code' => 'required|string|max:255|unique:programs,program_code,'.$program->id.',id',
  //     'parent_id' => 'nullable|exists:programs,id',
  //     'description' => 'nullable|string',
  //   ]);
  //   if ($program->update($att)) {
  //     return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => Program::DT_ID, 'close' => 'globalModal']);
  //   }
  // }


  public function destroy(Artist $artist)
  {
    try {
      if ($artist->delete()) {
        return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => Artist::DT_ID]);
      }
      return $this->sendError('Something Went Wrong');
    } catch (Throwable $e) {
      return $this->sendError('Server Error');
    }
  }


  public function timezones()
  {
    $timezone = new Timezonelist;

    return $timezone->toArray(false);
  }
  public function languages()
  {
    return config('languages');
  }

  public function currencies()
  {
    return [
      'USD' => 'US Dollar',
      'EUR' => 'Euro',
      'GBP' => 'British Pound',
      'JPY' => 'Japanese Yen',
      'AUD' => 'Australian Dollar',
      'CAD' => 'Canadian Dollar',
      'CHF' => 'Swiss Franc',
      'CNY' => 'Chinese Yuan',
      'SEK' => 'Swedish Krona',
      'NZD' => 'New Zealand Dollar',
      'MXN' => 'Mexican Peso',
      'SGD' => 'Singapore Dollar',
      'HKD' => 'Hong Kong Dollar',
      'NOK' => 'Norwegian Krone',
      'KRW' => 'South Korean Won',
      'TRY' => 'Turkish Lira',
      'RUB' => 'Russian Ruble',
      'INR' => 'Indian Rupee',
      'BRL' => 'Brazilian Real',
      'ZAR' => 'South African Rand',
      'TWD' => 'Taiwan New Dollar',
      'DKK' => 'Danish Krone',
      'PLN' => 'Polish Zloty',
      'THB' => 'Thai Baht',
      'IDR' => 'Indonesian Rupiah',
      'HUF' => 'Hungarian Forint',
      'CZK' => 'Czech Koruna',
      'ILS' => 'Israeli New Shekel',
      'CLP' => 'Chilean Peso',
      'PHP' => 'Philippine Peso',
      'AED' => 'United Arab Emirates Dirham',
      'COP' => 'Colombian Peso',
      'SAR' => 'Saudi Riyal',
      'MYR' => 'Malaysian Ringgit',
      'RON' => 'Romanian Leu',
      'NGN' => 'Nigerian Naira',
      'ARS' => 'Argentine Peso',
      'CRI' => 'Costa Rican Colon',
      'PEN' => 'Peruvian Nuevo Sol',
      'VND' => 'Vietnamese Dong',
      'UAH' => 'Ukrainian Hryvnia',
      'KWD' => 'Kuwaiti Dinar',
      'QAR' => 'Qatari Riyal',
    ];
  }

}
