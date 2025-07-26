<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Node;

use function sprintf;




final class CrapIndex
{
private readonly int $cyclomaticComplexity;
private readonly float $codeCoverage;

public function __construct(int $cyclomaticComplexity, float $codeCoverage)
{
$this->cyclomaticComplexity = $cyclomaticComplexity;
$this->codeCoverage = $codeCoverage;
}

public function asString(): string
{
if ($this->codeCoverage === 0.0) {
return (string) ($this->cyclomaticComplexity ** 2 + $this->cyclomaticComplexity);
}

if ($this->codeCoverage >= 95) {
return (string) $this->cyclomaticComplexity;
}

return sprintf(
'%01.2F',
$this->cyclomaticComplexity ** 2 * (1 - $this->codeCoverage / 100) ** 3 + $this->cyclomaticComplexity,
);
}
}
