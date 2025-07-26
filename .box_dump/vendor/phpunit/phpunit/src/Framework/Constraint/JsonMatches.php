<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function json_decode;
use function sprintf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\InvalidJsonException;
use PHPUnit\Util\Json;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
@no-named-arguments
*/
final class JsonMatches extends Constraint
{
private readonly string $value;

public function __construct(string $value)
{
$this->value = $value;
}




public function toString(): string
{
return sprintf(
'matches JSON string "%s"',
$this->value,
);
}







protected function matches(mixed $other): bool
{
[$error, $recodedOther] = Json::canonicalize($other);

if ($error) {
return false;
}

[$error, $recodedValue] = Json::canonicalize($this->value);

if ($error) {
return false;
}

return $recodedOther == $recodedValue;
}







protected function fail(mixed $other, string $description, ?ComparisonFailure $comparisonFailure = null): never
{
if ($comparisonFailure === null) {
[$error, $recodedOther] = Json::canonicalize($other);

if ($error) {
parent::fail($other, $description);
}

[$error, $recodedValue] = Json::canonicalize($this->value);

if ($error) {
parent::fail($other, $description);
}

$comparisonFailure = new ComparisonFailure(
json_decode($this->value),
json_decode($other),
Json::prettify($recodedValue),
Json::prettify($recodedOther),
'Failed asserting that two json values are equal.',
);
}

parent::fail($other, $description, $comparisonFailure);
}
}
