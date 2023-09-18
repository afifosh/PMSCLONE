<?php

namespace App\Traits;

use App\Support\LaravelBalance\Services\Accountant;
use App\Support\LaravelBalance\Services\TransactionProcessor;

trait FiananceTrait
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
}
