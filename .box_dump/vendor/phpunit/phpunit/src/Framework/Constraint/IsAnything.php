<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

/**
@no-named-arguments
*/
final class IsAnything extends Constraint
{










public function evaluate(mixed $other, string $description = '', bool $returnResult = false): ?bool
{
return $returnResult ? true : null;
}




public function toString(): string
{
return 'is anything';
}




public function count(): int
{
return 0;
}
}
