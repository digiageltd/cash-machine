<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Interfaces\TransactionInterface;

class BankTransaction implements TransactionInterface
{
    private array $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    public function validate()
    {
        $validator = Validator::make($this->requestData, [
            'transferDate' => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
            'customerName' => ['required', 'string'],
            'accountNumber' => ['required', 'string', 'alpha_num', 'size:6'],
            'amount' => ['required', 'numeric'],
        ], [
            'transferDate.required' => trans('cash_machine.transferDate.required'),
            'transferDate.date' => trans('cash_machine.transferDate.date'),
            'transferDate.after_or_equal' => trans('cash_machine.transferDate.after_or_equal'),
            'customerName.required' => trans('cash_machine.customerName.required'),
            'accountNumber.required' => trans('cash_machine.accountNumber.required'),
            'accountNumber.string' => trans('cash_machine.accountNumber.string'),
            'accountNumber.alpha_num' => trans('cash_machine.accountNumber.alpha_num'),
            'accountNumber.size' => trans('cash_machine.accountNumber.size'),
            'amount.required' => trans('cash_machine.amount.required'),
            'amount.numeric' => trans('cash_machine.amount.numeric'),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function amount(): float
    {
        return $this->requestData['amount'];
    }

    public function inputs(): array
    {
        return $this->requestData;
    }
}
