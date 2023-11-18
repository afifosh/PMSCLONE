<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\DataTables\Admin\MuseumsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Museum;
use Illuminate\Http\Request;
use App\Repositories\FileUploadRepository;
use App\Support\Timezonelist;
use App\Http\Requests\Admin\Museum\MuseumStoreRequest;
use Illuminate\Validation\Rule;

use Throwable;

class MuseumController extends Controller
{
    public function index(MuseumsDataTable $datatable)
    {
        return $datatable->render('admin.pages.museum.index');
    }

    public function create()
    {
        $data['museum'] = new Museum();
        $data['countries'] = ['' => 'Select Country'];
        $data['states'] = ['' => 'Select State'];
        $data['cities'] = ['' => 'Select City'];
        $data['timezones'] = $this->timezones();
        $data['languages'] = $this->languages();
        $data['currencies'] = $this->currencies();
        // You can add more data as needed
        return $this->sendRes('success', ['view_data' => view('admin.pages.museum.create', $data)->render(), 'JsMethods' => ['initIntlTel']]);
 
    }


    public function show(Museum $museum)
    {
        $data['museum'] = $museum;
        $data['timezones'] = $this->timezones();
        $data['languages'] = $this->languages();
        $data['currencies'] = $this->currencies();
        $data['countries'] = $museum->country_id ? Country::where('id', $museum->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
        $data['states'] = $museum->state_id ? State::where('id', $museum->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
        $data['cities'] = $museum->city_id ? City::where('id', $museum->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];

        return view('admin.pages.museum.show-profile', $data);
    }

    public function edit(Museum $museum)
    {
        $data['museum'] = $museum;
        $data['timezones'] = $this->timezones();
        $data['languages'] = $this->languages();
        $data['currencies'] = $this->currencies();
        $data['countries'] = $museum->country_id ? Country::where('id', $museum->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
        $data['states'] = $museum->state_id ? State::where('id', $museum->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
        $data['cities'] = $museum->city_id ? City::where('id', $museum->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];

        // You can add more data as needed
        return $this->sendRes('success', ['view_data' => view('admin.pages.museum.create', $data)->render(), 'JsMethods' => ['initIntlTel']]);
 
    }
    public function store(MuseumStoreRequest $request, FileUploadRepository $file_repo)
    {
        DB::beginTransaction(); // Start the transaction
    
        try {
            $museumData = $request->validated(); // Get validated data
    
            // Check for avatar in the request and process if present
            if ($request->hasFile('avatar')) {
                $path = Museum::MUSEUM_PATH; // Use the MUSEUM_PATH constant
                $MuseumImage = $path . '/' . $file_repo->addAttachment($request->file('avatar'), $path);
                $museumData['avatar'] = $MuseumImage; // Add avatar to studio data
            }
    
            // Create the Museum record
            $museum = Museum::create($museumData);
    
            DB::commit(); // Commit the transaction
    
            // Return a success response
            return $this->sendRes('Museum Created Successfully', ['event' => 'table_reload', 'table_id' => Museum::DT_ID, 'close' => 'globalModal']);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            // Handle exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    

    public function update(Museum $museum, MuseumStoreRequest $request)
    {
        $att = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('studios', 'name')->ignore($museum->id)],
            'website' => ['nullable', 'string', 'max:255', Rule::unique('studios', 'website')->ignore($museum->id)],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Update with your image validation rules
            'email' => ['nullable', 'string', 'max:255', Rule::unique('studios', 'email')->ignore($museum->id)],
            'phone' => 'nullable|phone',
            'phone_country' => 'required_with:phone',
            'address' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'state_id' => 'nullable|exists:states,id',
            'zip' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'language' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        $museum->update($att);

        return $this->sendRes('Updated Successfully', ['event' => 'redirect', 'url' => route('admin.museums.index')]);
    }

    public function destroy(Museum $museum)
    {
      try {
        if ($museum->delete()) {
          return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => Museum::DT_ID]);
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
