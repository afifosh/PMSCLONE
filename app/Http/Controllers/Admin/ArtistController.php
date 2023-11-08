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
    $data['timezones'] = $this->timezones();
    $data['languages'] = $this->languages();
    $data['currencies'] = $this->currencies();
    return view('admin.pages.artist.edit', $data);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'email' => 'required|email|unique:artists,email',
      'gender' => 'nullable|in:Male,Female,Other',
      'phone' => 'phone',
      'phone_country' => 'required_with:phone',
      'address' => 'nullable|string|max:255',
      'zip_code' => 'nullable|string|max:8',
      'country_id' => 'nullable|exists:countries,id',
      'state_id' => 'nullable|exists:states,id',
      'city_id' => 'nullable|exists:cities,id',
      'language' => 'required|string|max:255',
      'timezone' => 'nullable|string|max:255',
      'currency' => 'nullable|string|max:255',
    ]);


    if (Artist::create($att + ['added_by' => auth()->id()])) {
      return $this->sendRes('Created Successfully', ['event' => 'redirect', 'url' => route('admin.artists.index')]);
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

  public function update(Request $request, Artist $artist)
  {
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
      'language' => 'required|string|max:255',
      'timezone' => 'nullable|string|max:255',
      'currency' => 'nullable|string|max:255',
    ]);
    $artist->update($att);
    return $this->sendRes('Updated Successfully', ['event' => 'redirect', 'url' => route('admin.artists.index')]);
  }


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
