<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Exceptions;

use Exception;
use LaravelZero\Framework\Contracts\Exceptions\ConsoleExceptionContract;




final class ConsoleException extends Exception implements ConsoleExceptionContract
{





private $exitCode;






private $headers;




public function __construct(
int $exitCode,
?string $message = null,
array $headers = [],
?Exception $previous = null,
?int $code = 0
) {
$this->exitCode = $exitCode;
$this->headers = $headers;

parent::__construct($message, $code, $previous);
}




public function getExitCode(): int
{
return $this->exitCode;
}




public function getHeaders(): array
{
return $this->headers;
}




public function setHeaders(array $headers): ConsoleExceptionContract
{
$this->headers = $headers;

return $this;
}
}
