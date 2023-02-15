<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppsController extends Controller
{

    // File manager App
    public function file_manager()
    {
        $pageConfigs = [
            'pageHeader' => false,
            'contentLayout' => "content-left-sidebar",
            'pageClass' => 'file-manager-application',
        ];


   //return view('auth.two-factor-challenge-recovery');
        return view('admin.pages.fileManager.app-file-manager', ['pageConfigs' => $pageConfigs]);
    }


}
