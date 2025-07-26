<?php

declare(strict_types=1);










namespace Carbon\Exceptions;

use Throwable;

class BadComparisonUnitException extends UnitException
{





protected $unit;








public function __construct($unit, $code = 0, ?Throwable $previous = null)
{
$this->unit = $unit;

parent::__construct("Bad comparison unit: '$unit'", $code, $previous);
}






public function getUnit(): string
{
return $this->unit;
}
}
