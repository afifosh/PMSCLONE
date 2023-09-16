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

        return $transactionModel;
    }
}
