<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\User;
use App\Support\LaravelBalance\Models\AccountBalance;
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
      'groupedCompany' => [
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
      'Contract' => [
        'search' => 'subject',
        'select' => ['subject as text', 'id'],
        'dependent_column' => [
          'company_id'
        ],
      ],
      'Invoice' => [
        'search' => 'id',
        // concat INV-0000 and total - paid_amount
        'select' => [DB::raw("CONCAT('INV-', LPAD(id, 4, '0'), ' - UnPaid:', (total - paid_amount)/100) as text"), 'id'],
        'dependent_column' => 'contract_id',
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
      'AccountBalance' => [
        'search' => 'name',
        'select' => ['name as text', 'id'],
        'dependent_column' => [
          'program_id' => 'where has program',
        ]
      ],
      'Currency' => []
    ];
    if (!isset($allowedResources[$resource])) {
      return $this->sendError('Invalid resource');
    }

    if ($resource == 'Currency') {
      return $this->getCurrenciesList();
    } elseif ($resource == 'AccountBalance') {
      return $this->accountBalanceSelect($allowedResources);
    } elseif ($resource == 'Contract') {
      return $this->contractSelect($allowedResources);
    } elseif ($resource == 'groupedCompany')
      return $this->groupedCompanySelect($allowedResources);

    $model = 'App\Models\\' . $resource;

    $query = $model::query();

    return $query->when(request()->get('q'), function ($q) use ($allowedResources, $resource) {
      $q->where($allowedResources[$resource]['search'], 'like', '%' . request()->get('q') . '%');
    })->when(request()->dependent_id, function ($q) use ($allowedResources, $resource) {
      $q->where($allowedResources[$resource]['dependent_column'], request()->dependent_id);
    })
      ->when(request()->except, function ($q) use ($allowedResources, $resource) {
        $q->where('id', '!=', request()->except);
      })
      ->applyRequestFilters()
      ->select($allowedResources[$resource]['select'])->paginate(15, ['*'], 'page', request()->get('page'));
  }

  protected function contractSelect($allowedResources)
  {
    $resource = 'Contract';
    return Contract::when(request()->dependent == 'company_id', function ($q) {
      $q->where('assignable_type', Company::class)->where('assignable_id', request()->dependent_id);
    })
      ->when(request()->get('q'), function ($q) use ($allowedResources, $resource) {
        $q->where($allowedResources[$resource]['search'], 'like', '%' . request()->get('q') . '%');
      })
      ->when(request()->except, function ($q) use ($allowedResources, $resource) {
        $q->where('id', '!=', request()->except);
      })
      ->applyRequestFilters()
      ->select($allowedResources[$resource]['select'])->paginate(15, ['*'], 'page', request()->get('page'));
  }

  protected function accountBalanceSelect($allowedResources)
  {
    $resource = 'AccountBalance';
    return AccountBalance::when(request()->dependent == 'programId', function ($q) {
      $q->whereHas('programs', function ($q) {
        $q->where('programs.id', request()->dependent_id);
      });
    })
      ->when(request()->get('q'), function ($q) use ($allowedResources, $resource) {
        $q->where($allowedResources[$resource]['search'], 'like', '%' . request()->get('q') . '%');
      })
      ->when(request()->except, function ($q) use ($allowedResources, $resource) {
        $q->where('id', '!=', request()->except);
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

  public function groupedCompanySelect($allowedResources)
  {
    // get companies by type
    $companies = Company::applyRequestFilters()->select(['id', 'name', 'type'])->orderBy('type')->paginate(15, ['*'], 'page', request()->get('page'));

    // Create an array to store the formatted data
    $formattedData = [];

    // Initialize a variable to keep track of the current optgroup
    $currentType = null;

    foreach ($companies as $company) {
      // Check if the type has changed, indicating a new optgroup
      if ($company->type != $currentType) {
        $currentType = $company->type;
        $formattedData[] = [
          'text' => $currentType,
          'children' => [],
        ];
      }

      // Add the company as a child of the current optgroup
      $formattedData[count($formattedData) - 1]['children'][] = [
        'id' => $company->id,
        'text' => $company->name,
      ];
    }

    // Return the formatted data as a JSON response
    return response()->json([
      'data' => $formattedData,
      'pagination' => [
        'more' => $companies->hasMorePages(),
      ],
    ]);
  }
}
