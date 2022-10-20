<?php

namespace App\Exceptions;

use Exception;

class BadArgumentException extends Exception
{
    /**
     * @var array
     */
    private $errorDescriptions;

    public function __construct(string $message = "", int $code = 0, array $errorDescriptions, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorDescriptions = $errorDescriptions;
    }

    /**
     * @return array
     */
    public function getErrorDescriptions(): array
    {
        return $this->errorDescriptions;
    }
}
