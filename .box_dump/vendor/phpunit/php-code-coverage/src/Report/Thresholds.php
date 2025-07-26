<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Report;

use SebastianBergmann\CodeCoverage\InvalidArgumentException;

/**
@immutable
*/
final class Thresholds
{
private readonly int $lowUpperBound;
private readonly int $highLowerBound;

public static function default(): self
{
return new self(50, 90);
}




public static function from(int $lowUpperBound, int $highLowerBound): self
{
if ($lowUpperBound > $highLowerBound) {
throw new InvalidArgumentException(
'$lowUpperBound must not be larger than $highLowerBound',
);
}

return new self($lowUpperBound, $highLowerBound);
}

private function __construct(int $lowUpperBound, int $highLowerBound)
{
$this->lowUpperBound = $lowUpperBound;
$this->highLowerBound = $highLowerBound;
}

public function lowUpperBound(): int
{
return $this->lowUpperBound;
}

public function highLowerBound(): int
{
return $this->highLowerBound;
}
}
