<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

class InsufficientBalanceException extends ValidationException
{
    public $status = 422;
    public $message = 'Insufficient balance!';

    public function __construct(string $message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}