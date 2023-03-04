<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
  public function editDetails()
  {
    $data['details'] = CompanyDetail::firstOrNew();
    $data['form'] = 'company-details';
    return view('pages.company-profile.edit', $data);
  }
}
