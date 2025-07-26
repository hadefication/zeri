<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function is_string;
use function sprintf;
use function str_contains;
use function trim;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

/**
@no-named-arguments
*/
final class IsEqualCanonicalizing extends Constraint
{
private readonly mixed $value;

public function __construct(mixed $value)
{
$this->value = $value;
}













public function evaluate(mixed $other, string $description = '', bool $returnResult = false): bool
{



if ($this->value === $other) {
return true;
}

$comparatorFactory = ComparatorFactory::getInstance();

try {
$comparator = $comparatorFactory->getComparatorFor(
$this->value,
$other,
);

$comparator->assertEquals(
$this->value,
$other,
0.0,
true,
);
} catch (ComparisonFailure $f) {
if ($returnResult) {
return false;
}

throw new ExpectationFailedException(
trim($description . "\n" . $f->getMessage()),
$f,
);
}

return true;
}




public function toString(): string
{
if (is_string($this->value)) {
if (str_contains($this->value, "\n")) {
return 'is equal to <text>';
}

return sprintf(
"is equal to '%s'",
$this->value,
);
}

return sprintf(
'is equal to %s',
Exporter::export($this->value),
);
}
}
