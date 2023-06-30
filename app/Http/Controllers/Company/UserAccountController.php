<?php

namespace App\Http\Controllers\Company;

use App\DataTables\AuthenticationLogsDataTable;
use App\DataTables\NotificationsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Country;
use App\Models\User;
use App\Repositories\FileUploadRepository;
use App\Support\Timezonelist;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Illuminate\Http\JsonResponse;

class UserAccountController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, User $user_account, NotificationsDataTable $dataTable, AuthenticationLogsDataTable $authDataTable)
  {
    if ($request->t == 'security')
      return view('pages.account.account-edit-security');
    elseif ($request->t == 'notifications')
      return $dataTable->render('pages.account.account-notifications');
    elseif ($request->t == 'authlogs')
      return $authDataTable->render('pages.account.account-auth-logs');
    $data['timezones'] = $this->timezones();
    $data['languages'] = $this->languages();
    $data['currencies'] = $this->currencies();
    $data['countries'] = Country::get(['id', 'name']);

    return view('pages.account.account-edit-general', $data);
  }

  public function update(ProfileUpdateRequest $request, User $user_account, UpdatesUserProfileInformation $updater, FileUploadRepository $file_repo)
  {
    if ($request->hasFile('profile')) {
      $path = User::AVATAR_PATH . '/user-' . auth()->id();
      $avatar = $path . '/' . $file_repo->addAttachment($request->file('profile'), $path);
      $request->user()->update(['avatar' => $avatar]);
    }
    $updater->update($request->user(), Arr::except($request->validated(), ['phone_country', 'profile']));

    return $request->wantsJson()
      ? new JsonResponse('', 200)
      : back()->with('status', 'profile-information-updated');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)
  {
    //
  }

  protected function timezones()
  {
    $timezone = new Timezonelist;

    return $timezone->toArray(false);
  }
  protected function languages()
  {
    return [
      'en' => 'English',
      'fr' => 'French',
      'es' => 'Spanish',
      'de' => 'German',
      'it' => 'Italian',
      'pt' => 'Portuguese',
      'ru' => 'Russian',
      'ar' => 'Arabic',
      'tr' => 'Turkish',
      'zh' => 'Chinese',
      'ja' => 'Japanese',
      'ko' => 'Korean',
      'id' => 'Indonesian',
      'ms' => 'Malay',
      'th' => 'Thai',
      'vi' => 'Vietnamese',
      'pl' => 'Polish',
      'uk' => 'Ukrainian',
      'nl' => 'Dutch',
      'ro' => 'Romanian',
      'hu' => 'Hungarian',
      'cs' => 'Czech',
      'el' => 'Greek',
      'bg' => 'Bulgarian',
      'fi' => 'Finnish',
      'sv' => 'Swedish',
      'da' => 'Danish',
      'sk' => 'Slovak',
      'no' => 'Norwegian',
      'he' => 'Hebrew',
      'hi' => 'Hindi',
      'bn' => 'Bengali',
      'fil' => 'Filipino',
      'tl' => 'Filipino',
      'my' => 'Burmese',
      'km' => 'Khmer',
      'zh-TW' => 'Chinese (Taiwan)',
      'zh-CN' => 'Chinese (China)',
      'zh-HK' => 'Chinese (Hong Kong)',
      'zh-SG' => 'Chinese (Singapore)',
      'zh-MO' => 'Chinese (Macau)',
    ];
  }

  protected function currencies()
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
