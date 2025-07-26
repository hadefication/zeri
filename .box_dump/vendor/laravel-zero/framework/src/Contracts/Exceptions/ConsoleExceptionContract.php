<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Contracts\Exceptions;

use Symfony\Component\Console\Exception\ExceptionInterface;




interface ConsoleExceptionContract extends ExceptionInterface
{



public function getExitCode(): int;




public function getHeaders(): array;




public function setHeaders(array $headers): ConsoleExceptionContract;
}
