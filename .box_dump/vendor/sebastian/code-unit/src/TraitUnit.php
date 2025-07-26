<?php declare(strict_types=1);








namespace SebastianBergmann\CodeUnit;

/**
@immutable
*/
final readonly class TraitUnit extends CodeUnit
{
public function isTrait(): bool
{
return true;
}
}
