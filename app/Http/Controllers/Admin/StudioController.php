<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\StudiosDataTable;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Studio;
use Illuminate\Http\Request;
use App\Repositories\FileUploadRepository;
use App\Support\Timezonelist;
use Illuminate\Validation\Rule;

use Throwable;

class StudioController extends Controller
{
    public function index(StudiosDataTable $datatable)
    {
        return $datatable->render('admin.pages.studio.index');
    }

    public function create()
    {
        $data['studio'] = new Studio();
        $data['countries'] = ['' => 'Select Country'];
        $data['states'] = ['' => 'Select State'];
        $data['cities'] = ['' => 'Select City'];
        // You can add more data as needed
        return view('admin.pages.studio.edit', $data);
    }


    public function show(Studio $studio)
    {
        $data['studio'] = $studio;
        $data['timezones'] = $this->timezones();
        $data['languages'] = $this->languages();
        $data['currencies'] = $this->currencies();
        $data['countries'] = $studio->country_id ? Country::where('id', $studio->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
        $data['states'] = $studio->state_id ? State::where('id', $studio->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
        $data['cities'] = $studio->city_id ? City::where('id', $studio->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];
    
        return view('admin.pages.studio.show-profile', $data);
    }

    public function edit(Studio $studio)
    {
        $data['studio'] = $studio;
        $data['timezones'] = $this->timezones();
        $data['languages'] = $this->languages();
        $data['currencies'] = $this->currencies();
        $data['countries'] = $studio->country_id ? Country::where('id', $studio->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
        $data['states'] = $studio->state_id ? State::where('id', $studio->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
        $data['cities'] = $studio->city_id ? City::where('id', $studio->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];
    
        return view('admin.pages.studio.edit', $data);
    }


    public function store(Request $request)
    {
        $att = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:studios,name'],
            'website' => ['nullable', 'string', 'max:255', 'unique:studios,website'],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Update with your image validation rules
            'email' => ['nullable', 'string', 'max:255', 'unique:studios,email'],
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
    
        if (Studio::create($att)) {
            return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Studio::DT_ID, 'close' => 'globalModal']);
        }
    }
    
    public function update(Request $request, Studio $studio)
    {
        $att = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('studios', 'name')->ignore($studio->id)],
            'website' => ['nullable', 'string', 'max:255', Rule::unique('studios', 'website')->ignore($studio->id)],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Update with your image validation rules
            'email' => ['nullable', 'string', 'max:255', Rule::unique('studios', 'email')->ignore($studio->id)],
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
    
        $studio->update($att);
    

        $data['studio'] = $studio;
        $data['timezones'] = $this->timezones();
        $data['languages'] = $this->languages();
        $data['currencies'] = $this->currencies();
        $data['countries'] = $studio->country_id ? Country::where('id', $studio->country_id)->pluck('name', 'id')->prepend('Select Country', '') : ['' => 'Select Country'];
        $data['states'] = $studio->state_id ? State::where('id', $studio->state_id)->pluck('name', 'id')->prepend('Select State', '') : ['' => 'Select State'];
        $data['cities'] = $studio->city_id ? City::where('id', $studio->city_id)->pluck('name', 'id')->prepend('Select City', '') : ['' => 'Select City'];
    
    
        return $this->sendRes('Updated Successfully', [
            'view_data' => view('admin.pages.studio.edit', $data)->render(),
            'JsMethods' => ['initIntlTel'],
        ]);
    }
    
    public function destroy(Studio $studio)
    {
      try {
        if ($studio->delete()) {
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
  