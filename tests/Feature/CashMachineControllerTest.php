<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Services\CashTransaction;

use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;


class CashMachineControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase, DatabaseMigrations;

    /*
    / Cash Transaction Tests
    */
    public function test_store_cash_transaction_with_valid_data(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'cash',
            'quantity' => [
                1 => 2,
                5 => 3,
                10 => 1,
                50 => 4,
                100 => 0,
            ],
            'amount' => 227.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Assert that the response has a redirect status code
        $response->assertRedirect();

        // Assert that the stored transaction exists in the database
        $this->assertDatabaseHas('transactions', [
            'transaction_type' => $requestData['transaction_type'],
            'amount' => $requestData['amount'],
        ]);

        // Retrieve the stored transaction from the database
        $storedTransaction = Transaction::where([
            'transaction_type' => $requestData['transaction_type'],
            'amount' => $requestData['amount'],
        ])->latest()->first();

        // Assert that the response redirects to the correct route with the transaction ID
        $response->assertRedirect(route('transaction.show', ['transaction' => $storedTransaction]));
    }

    // It has 5 inputs for quantity of each type of banknotes
    public function test_store_cash_transaction_with_more_then_five_notes_data(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'cash',
            'quantity' => [
                1 => 2,
                5 => 3,
                10 => 1,
                50 => 6, // Quantity exceeds the maximum limit
                100 => 0,
            ]
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Get the validation errors from the session
        $errors = session('errors');

        // Assert that the validation errors contain the expected error message
        $this->assertTrue(
            $errors->has('quantity.50'),
            'The validation errors should contain the "quantity" field error for banknote 50.'
        );

    }

    // It can accept only banknotes of 1, 5, 10, 50, 100 for Cash source
    public function test_store_cash_transaction_with_invalid_banknotes(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'cash',
            'quantity' => [
                1 => 2,
                5 => 3,
                12 => 1, // Invalid banknote 12
                50 => 5,
                100 => 0,
            ],
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Get the validation errors from the session
        $errors = session('errors');

        // Assert that the validation errors contain the expected error message
        $this->assertTrue(
            $errors->has('quantity'),
            'Unknown banknotes detected!'
        );
    }

    // It has a limit of 10.000 of amount in Cash, everything more is declined
    public function test_store_cash_transaction_with_limit_exceeded(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'quantity' => [
                1 => 2,
                5 => 3,
                10 => 4,
                50 => 2,
                100 => 1,
            ],
        ];

        // Mock the CashTransaction class
        /** @var CashTransaction|MockObject $partialMock */
        $partialMock = $this->getMockBuilder(CashTransaction::class)
            ->setConstructorArgs([$requestData])
            ->onlyMethods(['isAmountLimitExceeded'])
            ->getMock();

        // Expect the isAmountLimitExceeded method to be called and return true
        $partialMock->expects($this->once())
            ->method('isAmountLimitExceeded')
            ->willReturn(true);

        // Assert that the validate method throws an exception with the expected error message
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(trans('cash_machine.cash.limit.exceeded'));

        // Call the validate method on the partial mock
        $partialMock->validate();
    }

    /*
    / Credit Card Transaction Tests
    */
    public function test_store_credit_card_transaction_with_valid_data(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'credit_card',
            'cardNumber' => '4123456789123456',
            'expirationDate' => '2026-6',
            'cardHolder' => 'Test Client',
            'cvv' => '123',
            'amount' => 660.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Assert that the response has a redirect status code
        $response->assertRedirect();

        // Assert that the stored transaction exists in the database
        $this->assertDatabaseHas('transactions', [
            'transaction_type' => $requestData['transaction_type'],
            'amount' => $requestData['amount'],
        ]);

        // Retrieve the stored transaction from the database
        $storedTransaction = Transaction::where([
            'transaction_type' => $requestData['transaction_type'],
            'amount' => $requestData['amount'],
        ])->latest()->first();

        // Assert that the response redirects to the correct route with the transaction ID
        $response->assertRedirect(route('transaction.show', ['transaction' => $storedTransaction]));
    }

    // It can accept Card Number with 16 digits
    public function test_store_credit_card_transaction_with_incorrect_number_of_digits(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'credit_card',
            'cardNumber' => '41234567891234', // 14 digits instead of 16
            'expirationDate' => '2026-6',
            'cardHolder' => 'Test Client',
            'cvv' => '123',
            'amount' => 450.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Get the validation errors from the session
        $errors = session('errors');

        // Assert that the validation errors contain the expected error message
        $this->assertTrue(
            $errors->has('cardNumber'),
            trans('cash_machine.cardNumber.size')
        );

    }

    // Only ones which starts with digit '4' (like 4123 4567 8912 3456)
    public function test_store_credit_card_transaction_with_incorrect_starting_digit(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'credit_card',
            'cardNumber' => '6123456789123456', // starts with 6 instead of 4
            'expirationDate' => '2026-6',
            'cardHolder' => 'Test Client',
            'cvv' => '123',
            'amount' => 550.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Get the validation errors from the session
        $errors = session('errors');

        // Assert that the validation errors contain the expected error message
        $this->assertTrue(
            $errors->has('cardNumber'),
            trans('cash_machine.cardNumber.regex')
        );
    }

    // Expiration Date must be at least 2 months bigger than current month
    public function test_store_credit_card_transaction_with_this_month_expiring_card(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'credit_card',
            'cardNumber' => '4123456789123456',
            'expirationDate' => date('Y-m'), // Expiring this month
            'cardHolder' => 'Test Client',
            'cvv' => '123',
            'amount' => 550.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Get the validation errors from the session
        $errors = session('errors');

        // Assert that the validation errors contain the expected error message
        $this->assertTrue(
            $errors->has('expirationDate'),
            trans('cash_machine.expirationDate.after_or_equal')
        );
    }

    // CVV (3 digits)
    public function test_store_credit_card_transaction_with_4_digit_cvv(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'credit_card',
            'cardNumber' => '4123456789123456',
            'expirationDate' => '2026-6',
            'cardHolder' => 'Test Client',
            'cvv' => '1234', // 4 digit CVV
            'amount' => 850.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Get the validation errors from the session
        $errors = session('errors');

        // Assert that the validation errors contain the expected error message
        $this->assertTrue(
            $errors->has('cvv'),
            trans('cash_machine.cvv.digits')
        );
    }

    /*
    / Bank Transaction Tests
    */
    public function test_store_bank_transaction_with_valid_data(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'bank_transfer',
            'transferDate' => date('Y-m-d'),
            'customerName' => 'Test Client',
            'accountNumber' => '52D3A5',
            'amount' => 2060.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Assert that the response has a redirect status code
        $response->assertRedirect();

        // Assert that the stored transaction exists in the database
        $this->assertDatabaseHas('transactions', [
            'transaction_type' => $requestData['transaction_type'],
            'amount' => $requestData['amount'],
        ]);

        // Retrieve the stored transaction from the database
        $storedTransaction = Transaction::where([
            'transaction_type' => $requestData['transaction_type'],
            'amount' => $requestData['amount'],
        ])->latest()->first();

        // Assert that the response redirects to the correct route with the transaction ID
        $response->assertRedirect(route('transaction.show', ['transaction' => $storedTransaction]));
    }

    // Test validation 8 alphanums instead of 6
    public function test_store_bank_transaction_with_8_digit_account_number(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'bank_transfer',
            'transferDate' => date('Y-m-d'),
            'customerName' => 'Test Client',
            'accountNumber' => '52D3A589', // 8 alpha-num instead of 6
            'amount' => 2060.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Get the validation errors from the session
        $errors = session('errors');

        // Assert that the validation errors contain the expected error message
        $this->assertTrue(
            $errors->has('accountNumber'),
            trans('cash_machine.accountNumber.alpha_num')
        );
    }

    // Transfer Date can't be older than current date
    public function test_store_bank_transaction_with_older_date(): void
    {
        // Create a fake transaction request data
        $requestData = [
            'transaction_type' => 'bank_transfer',
            'transferDate' => '2023-5-24',
            'customerName' => 'Test Client',
            'accountNumber' => '52D3A5', // 8 alpha-num instead of 6
            'amount' => 1060.00
        ];

        // Send a POST request to the store endpoint with the fake request data
        $response = $this->post(route('transaction.store'), $requestData);

        // Get the validation errors from the session
        $errors = session('errors');

        // Assert that the validation errors contain the expected error message
        $this->assertTrue(
            $errors->has('transferDate'),
            trans('cash_machine.transferDate.after_or_equal')
        );
    }

}
