<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\AuthenticationLogsDataTable;
use App\DataTables\NotificationsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Admin;
use App\Repositories\FileUploadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    return view('admin.pages.account.account-edit-general');
  }


  public function authLogs(AuthenticationLogsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.account.auth-logs');
  }

  public function updateProfilePic(ProfileUpdateRequest $request, UpdatesUserProfileInformation $updater, FileUploadRepository $file_repo)
  {
    $request->validate([
      'profile' => 'sometimes|mimetypes:image/*',
    ]);

    if($request->hasFile('profile'))
    {
      $path = Admin::AVATAR_PATH.'/user-'.auth()->id();
      $avatar = $path.'/'.$file_repo->addAttachment($request->file('profile'), $path);
      $request->user()->update(['avatar' => $avatar]);
    }
    $updater->update($request->user(), $request->all());

    return $request->wantsJson()
      ? new JsonResponse('', 200)
      : back()->with('status', 'profile-information-updated');
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
}
