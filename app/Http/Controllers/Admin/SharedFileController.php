<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RFPFile;
use Illuminate\Http\Request;

class SharedFileController extends Controller
{
    public function index(Request $request)
    {
      $files = RFPFile::availableShared()->filter($request->filter)->get();

      return view('admin.pages.rfp.file-manager', compact('files'));
    }
}
