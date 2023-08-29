<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResourceSearchController extends Controller
{
  public function index($resource)
  {
    $allowedResources = [
      'Company' => [
        'search' => 'name',
        'select' => ['id', 'name as text']
      ],
      'Client' => [
        'search' => 'email',
        'select' => ['email as text', 'id']
      ],
      'Project' => [
        'search' => 'name',
        'select' => ['name as text', 'id']
      ]
    ];
    // if (!in_array($resource, $allowedResources)) {
    //   return $this->sendError('Invalid resource');
    // }

    $model = 'App\Models\\' . $resource;

    $query = $model::query();

    return $query->when(request()->get('q'), function ($q) use ($allowedResources, $resource) {
      $q->where($allowedResources[$resource]['search'], 'like', '%' . request()->get('q') . '%');
    })->select($allowedResources[$resource]['select'])->paginate(15, ['*'], 'page', request()->get('page'));
  }
}
