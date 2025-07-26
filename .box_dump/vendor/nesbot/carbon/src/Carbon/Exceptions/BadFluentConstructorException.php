<?php

declare(strict_types=1);










namespace Carbon\Exceptions;

use BadMethodCallException as BaseBadMethodCallException;
use Throwable;

class BadFluentConstructorException extends BaseBadMethodCallException implements BadMethodCallException
{





protected $method;








public function __construct($method, $code = 0, ?Throwable $previous = null)
{
$this->method = $method;

parent::__construct(\sprintf("Unknown fluent constructor '%s'.", $method), $code, $previous);
}






public function getMethod(): string
{
return $this->method;
}
}
