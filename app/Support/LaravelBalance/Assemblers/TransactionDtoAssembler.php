<?php

namespace App\Support\LaravelBalance\Assemblers;

use App\Support\LaravelBalance\Models\Transaction;
use App\Support\LaravelBalance\Dto\TransactionDto;

class TransactionDtoAssembler
{
  public function dtoToModel(TransactionDto $transactionDto): Transaction
  {
    $transactionModel = new Transaction();
    $transactionModel->amount = $transactionDto->getAmount();
    $transactionModel->type = $transactionDto->getType();
    $transactionModel->title = $transactionDto->getTransactionTitle();
    $transactionModel->data = $transactionDto->getData();
    $transactionModel->description = $transactionDto->getDescription();
    $transactionModel->related_type = $transactionDto->getRelatedMorph()['type'] ?? null;
    $transactionModel->related_id = $transactionDto->getRelatedMorph()['id'] ?? null;

    return $transactionModel;
  }
}
