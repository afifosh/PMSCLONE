<?php

namespace App\Http\Controllers\Admin\Finance;

use Akaunting\Money\Money;
use App\DataTables\Admin\Finance\ProgramTransactionsDataTable;
use App\Http\Controllers\Controller;
use App\Support\LaravelBalance\Dto\TransactionDto;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Services\Accountant;
use App\Support\LaravelBalance\Services\TransactionProcessor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProgramTransactionController extends Controller
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
  public function index(AccountBalance $programAccount, ProgramTransactionsDataTable $dataTable)
  {
    $dataTable->programAccount = $programAccount;

    return $dataTable->render('admin.pages.finances.program-accounts.transactions.index', compact('programAccount'));
    // view('admin.pages.finances.program-accounts.transactions.index', compact('programAccount'));
  }

  public function create(AccountBalance $programAccount)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.program-accounts.transactions.create', compact('programAccount'))->render()]);
  }

  public function store(AccountBalance $programAccount, Request $request)
  {
    $request->validate([
      'amount' => 'required|numeric|gt:0',
      'type' => 'required|string|in:deposit,transfer',
      'account_id' => 'required|exists:account_balances,id',
    ]);

    $amount = Money::{$programAccount->currency ?? config('money.defaults.currency')}($request->amount * 1000, false)->getAmount();

    if ($request->type == 'deposit') {
      $this->transactionProcessor->create(
        $programAccount,
        new TransactionDto(
          $amount,
          'Credit',
          'Transfer',
          $request->description
        )
      );
      $this->transactionProcessor->create(
        AccountBalance::find($request->account_id),
        new TransactionDto(
          -$amount,
          'Debit',
          'Transfer',
          $request->description
        )
      );
    } elseif ($request->type == 'transfer') {

      // validate if the account has enough balance
      if ($programAccount->balance < $amount) {
        throw ValidationException::withMessages(['amount' => 'Onlye '.Money::{$programAccount->currency ?? config('money.defaults.currency')}($programAccount->balance, false)->format().' are available to transfer.']);
      }
      $this->transactionProcessor->create(
        $programAccount,
        new TransactionDto(
          -$amount,
          'Debit',
          'Transfer',
          $request->description,
        )
      );

      $this->transactionProcessor->create(
        AccountBalance::find($request->account_id),
        new TransactionDto(
          $amount,
          'Credit',
          'Transfer',
          $request->description,
        )
      );
    }

    return $this->sendRes('Transaction successful.', ['event' => 'table_reload', 'table_id' => 'program-transactions-table', 'close' => 'globalModal']);
  }
}
