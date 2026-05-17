<?php

namespace App\Exceptions;

use RuntimeException;

class StaleRecordException extends RuntimeException
{
    public function __construct(string $message = 'This record was modified by another user. Please reload and try again.')
    {
        parent::__construct($message);
    }
}
