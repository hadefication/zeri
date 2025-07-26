<?php declare(strict_types=1);








namespace SebastianBergmann\Comparator;

use function assert;
use function sprintf;
use UnitEnum;

final class EnumerationComparator extends Comparator
{
public function accepts(mixed $expected, mixed $actual): bool
{
return $expected instanceof UnitEnum &&
$actual instanceof UnitEnum &&
$expected::class === $actual::class;
}




public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
{
assert($expected instanceof UnitEnum);
assert($actual instanceof UnitEnum);

if ($expected === $actual) {
return;
}

throw new ComparisonFailure(
$expected,
$actual,
'',
'',
sprintf(
'Failed asserting that two values of enumeration %s are equal, %s does not match expected %s.',
$expected::class,
$actual->name,
$expected->name,
),
);
}
}
