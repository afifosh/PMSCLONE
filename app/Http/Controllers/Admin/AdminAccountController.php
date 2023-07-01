<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AuthenticationLogsDataTable;
use App\DataTables\NotificationsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Admin;
use App\Models\Country;
use App\Repositories\FileUploadRepository;
use App\Support\Timezonelist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class AdminAccountController extends Controller
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
   * @param  \App\Models\Admin  $admin
   * @return \Illuminate\Http\Response
   */
  public function show(Admin $admin)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Admin  $admin
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, NotificationsDataTable $dataTable)
  {
    if ($request->t == 'security')
      return view('admin.pages.account.account-edit-security');
    elseif ($request->t == 'notifications')
      return $dataTable->render('admin.pages.account.account-notifications');
    $data['timezones'] = $this->timezones();
    $data['languages'] = $this->languages();
    $data['currencies'] = $this->currencies();
    $data['countries'] = Country::get(['id', 'name']);

    return view('admin.pages.account.account-edit-general', $data);
  }


  public function authLogs(AuthenticationLogsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.account.auth-logs');
  }

  public function updateProfilePic(ProfileUpdateRequest $request, UpdatesUserProfileInformation $updater, FileUploadRepository $file_repo)
  {
    if ($request->hasFile('profile')) {
      $path = Admin::AVATAR_PATH . '/user-' . auth()->id();
      $avatar = $path . '/' . $file_repo->addAttachment($request->file('profile'), $path);
      $request->user()->update(['avatar' => $avatar]);
    }
    $updater->update($request->user(), Arr::except($request->validated(), ['phone_country', 'profile']));

    return $request->wantsJson()
      ? new JsonResponse('', 200)
      : back()->with('status', 'profile-information-updated');
  }

  public function updateEmail()
  {
    request()->validate([
      'email' => 'required|email|unique:admins,email',
      'password' => 'required',
    ]);
    if(!Hash::check(request()->password, auth()->user()->password))
      return response()->json(['errors' => ['password' => ['The provided password does not match your current password.']]], 422);

    auth()->user()->newEmail(request()->email);

    return $this->sendRes('Verification link sent to your new email address.', ['message' => '', 'event' => 'page_reload']);
  }

  public function resendVerificationEmail()
  {
    if (!auth()->user()->getPendingEmail())
      return $this->sendErr('Your email is already verified.');

    auth()->user()->resendPendingEmailVerificationMail();

    return $this->sendRes('Verification link sent to your email address.');
  }

  public function removePendingMail()
  {
    if (!auth()->user()->getPendingEmail())
      return $this->sendErr('Your email is already verified.');

    auth()->user()->clearPendingEmail();

    return $this->sendRes('Email removed.', ['event' => 'page_reload']);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Admin  $admin
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Admin $admin_account)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Admin  $admin
   * @return \Illuminate\Http\Response
   */
  public function destroy(Admin $admin)
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
