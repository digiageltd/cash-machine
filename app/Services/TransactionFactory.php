<?php

namespace App\Services;

use Exception;
use App\Interfaces\TransactionInterface;

class TransactionFactory
{
    /**
     * @throws Exception
     */
    public static function make(string $transactionType, array $requestData): TransactionInterface
    {
        return match ($transactionType) {
            'cash' => new CashTransaction($requestData),
            'credit_card' => new CardTransaction($requestData),
            'bank_transfer' => new BankTransaction($requestData),
            default => throw new Exception(trans('cash_machine.invalid.transaction.type')),
        };
    }
}
