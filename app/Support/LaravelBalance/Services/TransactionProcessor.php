<?php

namespace App\Support\LaravelBalance\Services;

use App\Support\LaravelBalance\Assemblers\TransactionDtoAssembler;
use App\Support\LaravelBalance\Dto\TransactionDto;
use App\Support\LaravelBalance\Models\AccountBalance;
use App\Support\LaravelBalance\Models\Transaction;

class TransactionProcessor
{
    /**
     * @var TransactionDtoAssembler
     */
    private $transactionDtoAssembler;

    /**
     * @param TransactionDtoAssembler $transactionDtoAssembler
     */
    public function __construct(TransactionDtoAssembler $transactionDtoAssembler)
    {
        $this->transactionDtoAssembler = $transactionDtoAssembler;
    }

    /**
     * @param AccountBalance $accountBalance
     * @param TransactionDto $transactionDto
     * @return Transaction
     */
    public function create(AccountBalance $accountBalance, TransactionDto $transactionDto): Transaction
    {
        $transaction = $this->transactionDtoAssembler->dtoToModel($transactionDto);

        // Associate transaction with account balance.
        $accountBalance->addTransaction($transaction);

        // Change account balance value.
        $balance = $accountBalance->getBalance();
        $balance = $balance->add($transaction->getAmount());
        $accountBalance->updateBalance(cMoney($balance->getAmount(), $balance->getCurrency()->getSymbol()));

        // Save models.
        $transaction->save();
        $accountBalance->save();

        return $transaction;
    }
}
