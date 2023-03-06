<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\CompanyInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Illuminate\Support\Facades\Session;

class InvitationController extends Controller
{
  public function accept($token)
  {
    $invitation = CompanyInvitation::where('token', $token)->where('valid_till', '>=', now())->whereNotIn('status', ['revoked', 'accepted'])->firstOrFail();
    return view('auth.accept-company-invitation', compact('invitation'));
  }

  public function acceptConfirm(Request $request, $token)
  {
    $request->validate([
      'password' => 'required|min:8|confirmed',
    ]);
    $invitation = CompanyInvitation::where('token', $token)->where('valid_till', '>=', now())->whereNotIn('status', ['revoked', 'accepted'])->firstOrFail();
    try {
      $company = $invitation->contactPerson->company;
      $user = $company->users()->create([
        'email' => $invitation->contactPerson->email,
        'password' => Hash::make($request->password),
      ]);
      $user->assignRole($invitation->role_id);
      $invitation->update(['status' => 'accepted']);
      Session::flush();
      auth()->login($user);
      $invitation->createLog('Invitation Accepted');
      return redirect()->route('pages-home');
    } catch (Throwable $e) {
      return back()->with('status', $e->getMessage());
    }
  }
}
