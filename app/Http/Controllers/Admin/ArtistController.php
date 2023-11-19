<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\DataTables\Admin\ArtistsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Artist;
use Illuminate\Http\Request;
use App\Repositories\FileUploadRepository;
use App\Support\Timezonelist;
use App\Http\Requests\Admin\Artist\ArtistStoreRequest;
use Illuminate\Validation\Rule;

use Throwable;

class ArtistController extends Controller
{
    public function index(ArtistsDataTable $datatable)
    {
        return $datatable->render('admin.pages.artist.index');
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
        // You can add more data as needed
        return $this->sendRes('success', ['view_data' => view('admin.pages.artist.create', $data)->render(), 'JsMethods' => ['initIntlTel']]);
 
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

        // You can add more data as needed
        return $this->sendRes('success', ['view_data' => view('admin.pages.artist.create', $data)->render(), 'JsMethods' => ['initIntlTel']]);
 
    }
    public function store(ArtistStoreRequest $request, FileUploadRepository $file_repo)
    {
        DB::beginTransaction(); // Start the transaction
    
        try {
            $artistData = $request->validated(); // Get validated data
    
            // Check for avatar in the request and process if present
            if ($request->hasFile('avatar')) {
                $path = Artist::ARTIST_PATH; // Use the ARTIST_PATH constant
                $ArtistImage = $path . '/' . $file_repo->addAttachment($request->file('avatar'), $path);
                $artistData['avatar'] = $ArtistImage; // Add avatar to studio data
            }
    
            // Create the Artist record
            $artist = Artist::create($artistData);
    
            DB::commit(); // Commit the transaction
    
            // Return a success response
            return $this->sendRes('Artist Created Successfully', ['event' => 'table_reload', 'table_id' => Artist::DT_ID, 'close' => 'globalModal']);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            // Handle exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    

    public function update(Artist $artist, ArtistStoreRequest $request, FileUploadRepository $file_repo)
    {
        DB::beginTransaction(); // Start the transaction
        
        try {
            $artistData = $request->validated(); // Get validated data
    
            // Check for avatar in the request and process if present
            if ($request->hasFile('avatar')) {
                $path = Artist::ARTIST_PATH; // Use the ARTIST_PATH constant
                $artistImage = $path . '/' . $file_repo->addAttachment($request->file('avatar'), $path);
                $artistData['avatar'] = $artistImage; // Update avatar in artist data
            }
    
            // Update the Artist record
            $artist->update($artistData);
    
            DB::commit(); // Commit the transaction
    
            // Return a success response
            return $this->sendRes('Updated Successfully', ['event' => 'redirect', 'url' => route('admin.artists.index')]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            // Handle exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
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
