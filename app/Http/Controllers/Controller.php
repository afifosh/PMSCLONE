<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @var BaseService
     */
    protected $service;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct() {

    }

    public function sendRes($message = 'Success', $data = [], $http_code = 200)
    {
      return response()->json(['success' => true,'message' => __($message), 'data' => $data], $http_code);
    }

    public function sendError($message = 'Failed', $data = [], $http_code = 500)
    {
      return response()->json(['success' => false,'message' => __($message), 'data' => $data], $http_code);
    }
}
