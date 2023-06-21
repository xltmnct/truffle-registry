<?php

namespace App\Exceptions;

use Exception;

class ApiErrorException extends Exception
{
    protected int $statusCode;
    protected array $errorData;

    public function __construct(int $statusCode, string $message = '', array $errorData = [], Exception $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->errorData = $errorData;

        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorData(): array
    {
        return $this->errorData;
    }
}
