<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function explode;
use function gettype;
use function is_array;
use function is_object;
use function is_string;
use function sprintf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use UnitEnum;

/**
@no-named-arguments
*/
final class IsIdentical extends Constraint
{
private readonly mixed $value;

public function __construct(mixed $value)
{
$this->value = $value;
}













public function evaluate(mixed $other, string $description = '', bool $returnResult = false): ?bool
{
$success = $this->value === $other;

if ($returnResult) {
return $success;
}

if (!$success) {
$f = null;


if (is_string($this->value) && is_string($other)) {
$f = new ComparisonFailure(
$this->value,
$other,
sprintf("'%s'", $this->value),
sprintf("'%s'", $other),
);
}


if ((is_array($this->value) && is_array($other)) || ($this->value instanceof UnitEnum && $other instanceof UnitEnum)) {
$f = new ComparisonFailure(
$this->value,
$other,
Exporter::export($this->value),
Exporter::export($other),
);
}

$this->fail($other, $description, $f);
}

return null;
}




public function toString(): string
{
if (is_object($this->value)) {
return 'is identical to an object of class "' .
$this->value::class . '"';
}

return 'is identical to ' . Exporter::export($this->value);
}







protected function failureDescription(mixed $other): string
{
if (is_object($this->value) && is_object($other)) {
return 'two variables reference the same object';
}

if (explode(' ', gettype($this->value), 2)[0] === 'resource' && explode(' ', gettype($other), 2)[0] === 'resource') {
return 'two variables reference the same resource';
}

if (is_string($this->value) && is_string($other)) {
return 'two strings are identical';
}

if (is_array($this->value) && is_array($other)) {
return 'two arrays are identical';
}

return parent::failureDescription($other);
}
}
