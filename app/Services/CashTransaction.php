<?php

namespace App\Services;

use App\Rules\BanknotesValidator;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Interfaces\TransactionInterface;
use App\Models\Transaction;

class CashTransaction implements TransactionInterface
{
    private array $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    public function validate()
    {
        $validator = Validator::make($this->requestData, [
            'quantity' => ['required', 'array', new BanknotesValidator],
            'quantity.1' => 'integer|max:5',
            'quantity.5' => 'integer|max:5',
            'quantity.10' => 'integer|max:5',
            'quantity.50' => 'integer|max:5',
            'quantity.100' => 'integer|max:5',
        ], [
            'quantity.required' => trans('cash_machine.quantity.required'),
            'quantity.array' => trans('cash_machine.quantity.array'),
            'quantity.1.integer' => trans('cash_machine.quantity.1.integer'),
            'quantity.1.max' => trans('cash_machine.quantity.1.max'),
            'quantity.5.integer' => trans('cash_machine.quantity.5.integer'),
            'quantity.5.max' => trans('cash_machine.quantity.5.max'),
            'quantity.10.integer' => trans('cash_machine.quantity.10.integer'),
            'quantity.10.max' => trans('cash_machine.quantity.10.max'),
            'quantity.50.integer' => trans('cash_machine.quantity.50.integer'),
            'quantity.50.max' => trans('cash_machine.quantity.50.max'),
            'quantity.100.integer' => trans('cash_machine.quantity.100.integer'),
            'quantity.100.max' => trans('cash_machine.quantity.100.max'),
        ]);


        // Check if the validator fails
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Calculate the total amount
        $totalAmount = $this->calculateTotalAmount();

        // Perform the check for amount limit exceeded
        if ($this->isAmountLimitExceeded($totalAmount)) {
            throw new Exception(trans('cash_machine.cash.limit.exceeded'));
        }
    }

    public function amount(): float
    {
        return $this->calculateTotalAmount();
    }

    public function inputs(): array
    {
        return $this->requestData;
    }

    private function calculateTotalAmount(): int
    {
        $amount = 0;
        $banknotes = [1, 5, 10, 50, 100];

        foreach ($banknotes as $banknote) {
            $amount += $this->requestData['quantity'][$banknote] * $banknote;
        }

        return $amount;
    }

    public function isAmountLimitExceeded(float $amount): bool
    {
        // Implement the logic to check if the amount limit is exceeded
        $totalAmount = Transaction::where('transaction_type', 'cash')->sum('amount');

        return ($totalAmount + $amount) > 10000;
    }
}
