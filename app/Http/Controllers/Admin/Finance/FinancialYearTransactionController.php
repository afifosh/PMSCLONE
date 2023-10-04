<?php

namespace App\Http\Controllers\Admin\Finance;

use Akaunting\Money\Money;
use App\DataTables\Admin\Finance\FinancialYearTransactionsDataTable;
use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use App\Support\LaravelBalance\Dto\TransactionDto;
use Illuminate\Http\Request;
use App\Support\LaravelBalance\Services\Accountant;
use App\Support\LaravelBalance\Services\TransactionProcessor;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Models\Transaction;

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

    return $dataTable->render('admin.pages.finances.financial-years.transactions.index', ['accountBalance' => $financialYear->defaultCurrencyAccount[0]]);
    // view('admin.pages.finances.financial-years.transactions.index', compact('financialYear'));
  }

  public function create(AccountBalance $financialYear)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.financial-years.transactions.create', compact('financialYear'))->render()]);
  }

  public function store(AccountBalance $financialYear, Request $request)
  {
    $validated = $request->validate([
      'amount' => 'required|numeric|gt:0',
      'type' => 'required|string|in:deposit,withdraw,transfer',
      'account_id' => 'required_if:type,transfer|exists:account_balances,id',
      'description' => 'nullable|string|max:255',
    ]);

    if ($validated['type'] == 3 && $validated['account_id'] == $financialYear->id) {
      return $this->sendErr('You cannot transfer to the same account.');
    }

    $amount = Money::{$financialYear->currency ?? config('money.defaults.currency')}($validated['amount'] * 1000, false)->getAmount();

    if ($request->type == 'deposit' || $request->type == 'withdraw')
      $this->transactionProcessor->create(
        $financialYear,
        new TransactionDto(
          $request->type == 'deposit' ? $amount : -$amount,
          $request->type == 'deposit' ? 'Credit' : 'Debit',
          $request->type == 'deposit' ? 'Deposit' : 'Withdraw',
          $request->description
        )
      );
    elseif($request->type == 'transfer'){
      $this->transactionProcessor->create(
        $financialYear,
        new TransactionDto(
          -$amount,
          'Debit',
          'Transfer',
          $request->description,
        )
      );

      $this->transactionProcessor->create(
        AccountBalance::find($validated['account_id']),
        new TransactionDto(
          $amount,
          'Credit',
          'Transfer',
          $request->description,
        )
      );
    }

    return $this->sendRes('Transaction added successfully', ['event' => 'table_reload', 'table_id' => 'financial-years-table', 'close' => 'globalModal']);
  }

  public function show($accountBalance, Transaction $transaction)
  {
    $transaction->load('accountBalance');
    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.financial-years.transactions.show', compact('transaction'))->render()]);
  }
}
