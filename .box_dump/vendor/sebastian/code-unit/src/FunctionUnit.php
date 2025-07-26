<?php declare(strict_types=1);








namespace SebastianBergmann\CodeUnit;

/**
@immutable
*/
final readonly class FunctionUnit extends CodeUnit
{
public function isFunction(): bool
{
return true;
}
}
