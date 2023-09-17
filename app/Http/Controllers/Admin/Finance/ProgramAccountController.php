<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\ProgramAccountsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Services\Accountant;
use App\Support\LaravelBalance\Services\TransactionProcessor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramAccountController extends Controller
{
  /**
   * @var Accountant
   */
  private $accountant;

  /**
   * @var TransactionProcessor
   */
  private $transactionProcessor;

  public function __construct(Accountant $accountant, TransactionProcessor $transactionProcessor)
  {
    $this->accountant = $accountant;
    $this->transactionProcessor = $transactionProcessor;
  }

  public function index(ProgramAccountsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.finances.program-accounts.index');
    // view('admin.pages.finances.program-accounts.index')
  }

  public function create()
  {
    $accountBalance = new AccountBalance();
    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.program-accounts.create', compact('accountBalance'))->render()]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'label' => 'required|string',
      'currency' => ['required', 'string', 'max:3', Rule::in(array_keys(config('money.currencies')))],
      'holders' => 'required|array',
      'holders.*' => 'required|exists:programs,id'
    ]);

    $program_ids = filterInputIds($request->holders);

    $this->accountant->createAccount(
      Program::whereIn('id', $program_ids)->get(),
      ['currency' => $request->currency, 'name' => $request->label]
    );

    return $this->sendRes('Account created successfully.', ['event' => 'table_reload', 'table_id' => 'program-accounts-table', 'close' => 'globalModal']);
  }
}
