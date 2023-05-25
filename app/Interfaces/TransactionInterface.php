<?php

namespace App\Interfaces;

use Exception;

interface TransactionInterface
{
    /**
     * Validate the transaction inputs.
     *
     * @throws Exception if the validation fails.
     */
    public function validate();

    /**
     * Calculate and return the total amount for the transaction.
     *
     * @return float The total amount.
     */
    public function amount(): float;

    /**
     * Get the inputs of the transaction.
     *
     * @return array The transaction inputs.
     */
    public function inputs(): array;
}
