<?php

declare(strict_types=1);










namespace Carbon\Exceptions;

use RuntimeException as BaseRuntimeException;
use Throwable;

class ImmutableException extends BaseRuntimeException implements RuntimeException
{





protected $value;








public function __construct($value, $code = 0, ?Throwable $previous = null)
{
$this->value = $value;
parent::__construct("$value is immutable.", $code, $previous);
}






public function getValue(): string
{
return $this->value;
}
}
