<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function assert;
use function gettype;
use function is_int;
use function is_object;
use function sprintf;
use function str_replace;
use function strpos;
use function strtolower;
use function substr;
use Countable;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\SelfDescribing;
use PHPUnit\Util\Exporter;
use ReflectionObject;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
@no-named-arguments
*/
abstract class Constraint implements Countable, SelfDescribing
{












public function evaluate(mixed $other, string $description = '', bool $returnResult = false): ?bool
{
$success = false;

if ($this->matches($other)) {
$success = true;
}

if ($returnResult) {
return $success;
}

if (!$success) {
$this->fail($other, $description);
}

return null;
}




public function count(): int
{
return 1;
}







protected function matches(mixed $other): bool
{
return false;
}






protected function fail(mixed $other, string $description, ?ComparisonFailure $comparisonFailure = null): never
{
$failureDescription = sprintf(
'Failed asserting that %s.',
$this->failureDescription($other),
);

$additionalFailureDescription = $this->additionalFailureDescription($other);

if ($additionalFailureDescription) {
$failureDescription .= "\n" . $additionalFailureDescription;
}

if (!empty($description)) {
$failureDescription = $description . "\n" . $failureDescription;
}

throw new ExpectationFailedException(
$failureDescription,
$comparisonFailure,
);
}







protected function additionalFailureDescription(mixed $other): string
{
return '';
}










protected function failureDescription(mixed $other): string
{
return Exporter::export($other) . ' ' . $this->toString();
}













protected function toStringInContext(Operator $operator, mixed $role): string
{
return '';
}













protected function failureDescriptionInContext(Operator $operator, mixed $role, mixed $other): string
{
$string = $this->toStringInContext($operator, $role);

if ($string === '') {
return '';
}

return Exporter::export($other) . ' ' . $string;
}





























































protected function reduce(): self
{
return $this;
}




protected function valueToTypeStringFragment(mixed $value): string
{
if (is_object($value)) {
$reflector = new ReflectionObject($value);

if ($reflector->isAnonymous()) {
$name = str_replace('class@anonymous', '', $reflector->getName());

$length = strpos($name, '$');

assert(is_int($length));

$name = substr($name, 0, $length);

return 'an instance of anonymous class created at ' . $name . ' ';
}

return 'an instance of class ' . $reflector->getName() . ' ';
}

$type = strtolower(gettype($value));

if ($type === 'double') {
$type = 'float';
}

if ($type === 'resource (closed)') {
$type = 'closed resource';
}

return match ($type) {
'array', 'integer' => 'an ' . $type . ' ',
'boolean', 'closed resource', 'float', 'resource', 'string' => 'a ' . $type . ' ',
'null' => 'null ',
default => 'a value of ' . $type . ' ',
};
}
}
