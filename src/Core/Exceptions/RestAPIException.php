<?php

namespace ATCM\Core\Exceptions;

class RestAPIException extends \Exception
{
    protected int $httpCode;

    public function __construct(string $message, int $code = 0, int $httpCode = 400, \Exception $previous = null)
    {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code} | HTTP {$this->httpCode}]: {$this->message}\n";
    }

    public function getHttpCode() {
        return $this->httpCode;
    }
}