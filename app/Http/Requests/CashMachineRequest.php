<?php

namespace App\Http\Requests;

use App\Rules\BanknotesValidator;
use Illuminate\Foundation\Http\FormRequest;

class CashMachineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'transaction_type' => 'required|in:cash,credit_card,bank_transfer',
            'quantity' => [
                'required_if:transaction_type,cash',
                'array',
                'size:5'
            ],
            'cardNumber' => 'required_if:transaction_type,credit_card|numeric|digits:16|starts_with:4',
            'expirationDate' => 'required_if:transaction_type,credit_card|date|after:2 months',
            'cvv' => 'required_if:transaction_type,credit_card|numeric|digits:3',
            'transferDate' => 'required_if:transaction_type,bank_transfer|date|after_or_equal:today',
            'customerName' => 'required_if:transaction_type,bank_transfer|string',
            'accountNumber' => 'required_if:transaction_type,bank_transfer|alpha_num|size:6',
            'amount' => 'required_if:transaction_type,bank_transfer,credit_card|numeric|min:0',
        ];
    }
}
