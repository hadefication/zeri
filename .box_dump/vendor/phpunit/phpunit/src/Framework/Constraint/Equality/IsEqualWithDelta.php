<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function sprintf;
use function trim;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

/**
@no-named-arguments
*/
final class IsEqualWithDelta extends Constraint
{
private readonly mixed $value;
private readonly float $delta;

public function __construct(mixed $value, float $delta)
{
$this->value = $value;
$this->delta = $delta;
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
$this->delta,
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
return sprintf(
'is equal to %s with delta <%F>',
Exporter::export($this->value),
$this->delta,
);
}
}
