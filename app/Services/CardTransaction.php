<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Interfaces\TransactionInterface;

class CardTransaction implements TransactionInterface
{
    private array $requestData;

    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    public function validate()
    {
        $validator = Validator::make($this->requestData, [
            'cardNumber' => ['required', 'string', 'size:16', 'regex:/^4/'],
            'expirationDate' => ['required', 'date', 'after_or_equal:' . now()->addMonths(2)->format('Y-m')],
            'cardHolder' => ['required', 'string'],
            'cvv' => ['required', 'numeric', 'digits:3'],
            'amount' => ['required', 'numeric']
        ], [
            'cardNumber.required' => trans('cash_machine.cardHolder.required'),
            'cardNumber.size' => trans('cash_machine.cardNumber.size'),
            'cardNumber.regex' => trans('cash_machine.cardNumber.regex'),
            'expirationDate.required' => trans('cash_machine.expirationDate.required'),
            'expirationDate.date' => trans('cash_machine.expirationDate.date'),
            'expirationDate.after_or_equal' => trans('cash_machine.expirationDate.after_or_equal'),
            'cardHolder.required' => trans('cash_machine.cardHolder.required'),
            'cvv.required' => trans('cash_machine.cvv.required'),
            'cvv.numeric' => trans('cash_machine.cvv.numeric'),
            'cvv.digits' => trans('cash_machine.cvv.digits'),
            'amount.required' => trans('cash_machine.amount.required'),
            'amount.numeric' => trans('cash_machine.amount.numeric')
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
