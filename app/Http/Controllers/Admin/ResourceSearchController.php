<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
      ],
      'Program' => [
        'search' => 'name',
        'select' => ['name as text', 'id']
      ],
      'ProjectCategory' => [
        'search' => 'name',
        'select' => ['name as text', 'id']
      ],
      'Country' => [
        'search' => 'name',
        'select' => [DB::raw("CONCAT(UCASE(LEFT(name, 1)), LCASE(SUBSTRING(name, 2))) as text"), 'id']
      ],
      'State' => [
        'search' => 'name',
        'select' => [DB::raw("CONCAT(UCASE(LEFT(name, 1)), LCASE(SUBSTRING(name, 2))) as text"), 'id'],
        'dependent_column' => 'country_id'
      ],
      'City' => [
        'search' => 'name',
        'select' => [DB::raw("CONCAT(UCASE(LEFT(name, 1)), LCASE(SUBSTRING(name, 2))) as text"), 'id'],
        'dependent_column' => 'state_id'
      ],
      'Currency' => []
    ];
    if (!isset($allowedResources[$resource])) {
      return $this->sendError('Invalid resource');
    }

    if ($resource == 'Currency') {
      return $this->getCurrenciesList();
    }

    $model = 'App\Models\\' . $resource;

    $query = $model::query();

    return $query->when(request()->get('q'), function ($q) use ($allowedResources, $resource) {
      $q->where($allowedResources[$resource]['search'], 'like', '%' . request()->get('q') . '%');
    })->when(request()->dependent_id, function ($q) use ($allowedResources, $resource) {
      $q->where($allowedResources[$resource]['dependent_column'], request()->dependent_id);
    })
    ->select($allowedResources[$resource]['select'])->paginate(15, ['*'], 'page', request()->get('page'));
  }

  public function userSelect($resource)
  {
    $allowedResources = [
      'Admin' => [
        'search' => ['email', 'first_name', 'last_name'],
        'select' => ['id', 'email as text', 'first_name', 'last_name', DB::raw('CONCAT(first_name, " ", last_name) as full_name'), 'avatar']
      ],
      'Client' => [
        'search' => ['email', 'first_name', 'last_name'],
        'select' => ['id', 'email as text', 'first_name', 'last_name']
      ],
      'Company' => [
        'search' => ['email', 'name'],
        'select' => ['id', 'email as text', 'name', 'name as full_name']
      ],
    ];
    if (!isset($allowedResources[$resource])) {
      return $this->sendError('Invalid resource');
    }

    $model = 'App\Models\\' . $resource;

    $query = $model::query();

    return $query->when(request()->get('q'), function ($q) use ($allowedResources, $resource) {
      $q->where(function ($q) use ($allowedResources, $resource) {
        foreach ($allowedResources[$resource]['search'] as $search) {
          $q->orWhere($search, 'like', '%' . request()->get('q') . '%');
        }
      });
    })->select($allowedResources[$resource]['select'])->paginate(15, ['*'], 'page', request()->get('page'));
  }

  public function getCurrenciesList()
  {
    $q = request()->get('q');
    $currencies = config('money.currencies');

    $data = [
      "current_page" => 1, "total" => 1, "per_page" => 2
    ];

    $data['data'] = collect($currencies)->filter(function ($currency, $symbol) use ($q) {
      return strpos(strtolower($currency['name']), strtolower($q)) !== false || strpos(strtolower($symbol), strtolower($q)) !== false;
    })->map(function ($currency, $symbol) {
      return [
        'id' => $symbol,
        'text' => '(' . $symbol . ') - ' . $currency['name']
      ];
    })->values();

    return $data;
  }
}
