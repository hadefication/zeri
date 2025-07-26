<?php

declare(strict_types=1);










namespace Carbon\Exceptions;

use BadMethodCallException as BaseBadMethodCallException;
use Throwable;

class BadFluentSetterException extends BaseBadMethodCallException implements BadMethodCallException
{





protected $setter;








public function __construct($setter, $code = 0, ?Throwable $previous = null)
{
$this->setter = $setter;

parent::__construct(\sprintf("Unknown fluent setter '%s'", $setter), $code, $previous);
}






public function getSetter(): string
{
return $this->setter;
}
}
