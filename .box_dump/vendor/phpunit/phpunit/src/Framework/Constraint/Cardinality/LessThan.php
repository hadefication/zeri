<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use PHPUnit\Util\Exporter;

/**
@no-named-arguments
*/
final class LessThan extends Constraint
{
private readonly mixed $value;

public function __construct(mixed $value)
{
$this->value = $value;
}




public function toString(): string
{
return 'is less than ' . Exporter::export($this->value);
}





protected function matches(mixed $other): bool
{
return $this->value > $other;
}
}
