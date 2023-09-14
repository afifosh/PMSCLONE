<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\FinancialYearsDataTable;
use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Vuer\LaravelBalance\Services\Accountant;
use Vuer\LaravelBalance\Services\TransactionProcessor;
use Money\Currency;

class FinancialYearController extends Controller
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

  /**
   * Display a listing of the resource.
   */
  public function index(FinancialYearsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.finances.financial-years.index');
    // view('admin.pages.finances.financial-years.index')
  }

  public function create()
  {
    $financialYear = new FinancialYear();

    $currency = [config('money.defaults.currency') => config('money.defaults.currencyText')];

    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.financial-years.create', compact('financialYear', 'currency'))->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'label' => 'required|string|max:100',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after:start_date',
      'initial_balance' => 'required|numeric',
      'currency' => ['required', 'string', Rule::In(array_keys(config('money.currencies')))],
    ]);

    $financialYear = FinancialYear::create($validated);

    $account = $this->accountant->getAccountOrCreate($financialYear, new Currency($validated['currency']));

    $this->transactionProcessor->create($account, new \Vuer\LaravelBalance\Dto\TransactionDto($financialYear->getRawOriginal('initial_balance'), 1));


    return $this->sendRes(__('Financial year created successfully'), ['event' => 'table_reload', 'table_id' => 'financial-years-table', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(FinancialYear $financialYear)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(FinancialYear $financialYear)
  {
    $financialYear->load('defaultCurrencyAccount');
    $currency = [$financialYear->defaultCurrencyAccount->currency => '(' . $financialYear->defaultCurrencyAccount->currency . ') - ' . config('money.currencies.' . $financialYear->defaultCurrencyAccount->currency . '.name')];
    $selected_currency = $financialYear->defaultCurrencyAccount->currency;
    return $this->sendRes('success', ['view_data' => view('admin.pages.finances.financial-years.create', compact('financialYear', 'currency'))->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, FinancialYear $financialYear)
  {
    $validated = $request->validate([
      'label' => 'required|string|max:100',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after:start_date',
      'currency' => ['required', 'string', Rule::In(array_keys(config('money.currencies')))],
    ]);

    $financialYear->update($validated);

    $financialYear->load('defaultCurrencyAccount');
    $financialYear->defaultCurrencyAccount->forceFill(['currency' => $request->currency]);
    $financialYear->defaultCurrencyAccount->save();

    return $this->sendRes(__('Financial year updated successfully'), ['event' => 'table_reload', 'table_id' => 'financial-years-table', 'close' => 'globalModal']);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(FinancialYear $financialYear)
  {
    $financialYear->load('accountBalances');

    foreach ($financialYear->accountBalances as $accountBalance) {
      $accountBalance->transactions()->delete();
      $accountBalance->delete();
    }
    $financialYear->delete();

    return $this->sendRes(__('Financial year deleted successfully'), ['event' => 'table_reload', 'table_id' => 'financial-years-table']);
  }
}
