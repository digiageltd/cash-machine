<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashMachineRequest;
use App\Models\Transaction;
use App\Services\TransactionFactory;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Validation\ValidationException;
use Log;
use Exception;
use Illuminate\Support\Facades\DB;


class CashMachineController extends Controller
{
    public function index(): Renderable
    {
        return view('home');
    }

    public function transaction(string $type): Renderable
    {
        $quantities = null;

        if ($type == 'cash') {
            $quantities = [1, 5, 10, 50, 100];
        }

        return view('transaction')->with([
            'type' => $type,
            'quantities' => $quantities
        ]);
    }

    public function store(CashMachineRequest $request)
    {
        try {
            // Create a transaction based on the transaction type and request data
            $transaction = TransactionFactory::make($request->transaction_type, $request->all());

            // Validate the transaction inputs
            $transaction->validate();

            // Calculate the transaction amount
            $amount = $transaction->amount();


            // Perform the check for amount limit exceeded
            if ($this->isAmountLimitExceeded($amount)) {
                return $this->transactionError()->withErrors(['amount' => trans('cash_machine.amount.limit.exceeded')]);
            }

            // Store the transaction in the database
            $storedTransaction = $this->storeTransaction($request->transaction_type, $amount, $transaction->inputs());

            // Redirect to a new page with transaction details
            return redirect()->route('transaction.show', ['transaction' => $storedTransaction]);
        } catch (ValidationException $e) {
            Log::error('There was a problem with the transaction validation: ' . $e->validator->errors());
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        } catch (Exception $e) {
            Log::error('There was a problem with the transaction: ' . $e->getMessage());
            return redirect()->route('transaction.error');
        }
    }

    public function show(Transaction $transaction)
    {
        return view('show')->with([
            'transaction' => $transaction
        ]);
    }

    public function transactionError()
    {
        return view('error');
    }

    private function isAmountLimitExceeded(float $amount): bool
    {
        // Implementing the logic to check if the amount limit is exceeded
        $totalAmount = Transaction::sum('amount');
        return ($totalAmount + $amount) > 20000;
    }

    private function storeTransaction(string $transactionType, float $amount, array $inputs): object|null
    {
        try {
            return DB::transaction(function () use ($transactionType, $amount, $inputs) {
                return Transaction::create([
                    'transaction_type' => $transactionType,
                    'amount' => $amount,
                    'inputs' => $inputs,
                ]);
            });
        } catch (Exception $e) {
            Log::error('Transaction rolled back: ' . $e->getMessage());
            return null;
        }
    }
}
