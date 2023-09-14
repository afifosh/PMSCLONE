<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\FinancialYearTransactionsDataTable;
use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use App\Models\Program;
use Illuminate\Http\Request;
use Vuer\LaravelBalance\Services\Accountant;
use Vuer\LaravelBalance\Services\TransactionProcessor;
use Money\Currency;

class FinancialYearTransactionController extends Controller
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

  public function index(FinancialYear $financialYear, FinancialYearTransactionsDataTable $dataTable)
  {
    $financialYear->load('defaultCurrencyAccount');
    $dataTable->financialYear = $financialYear;

    return $dataTable->render('admin.pages.finances.financial-years.transactions.index');
    // view('admin.pages.finances.financial-years.transactions.index', compact('financialYear'));
  }

  public function create(FinancialYear $financialYear)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.financial-years.transactions.create', compact('financialYear'))->render()]);
  }

  public function store(FinancialYear $financialYear, Request $request)
  {
    $validated = $request->validate([
      'amount' => 'required|numeric|gt:0',
      'type' => 'required|in:1,2,3',
      'program_id' => 'nullable|required_if:type,3|exists:programs,id',
    ],[
      'program_id.required_if' => 'The program field is required when type is transfer.'
    ]);

    $validated['amount'] = $validated['type'] == 1 ? $validated['amount'] * 100 : -$validated['amount'] * 100;

    $financialYear->load('defaultCurrencyAccount');

    $this->transactionProcessor->create($financialYear->defaultCurrencyAccount, new \Vuer\LaravelBalance\Dto\TransactionDto($validated['amount'], $validated['type'] == 3 ? 2 : 1));

    if($validated['type'] == 3){
      $account = $this->accountant->getAccountOrCreate(Program::find($request->program_id), new Currency($financialYear->defaultCurrencyAccount->currency));
      $this->transactionProcessor->create($account, new \Vuer\LaravelBalance\Dto\TransactionDto(-$validated['amount'], 2));
    }

    return $this->sendRes('Transaction added successfully', ['event' => 'table_reload', 'table_id' => 'financial-years-table', 'close' => 'globalModal']);
  }
}
