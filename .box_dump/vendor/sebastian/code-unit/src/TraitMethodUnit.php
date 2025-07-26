<?php declare(strict_types=1);








namespace SebastianBergmann\CodeUnit;

/**
@immutable
*/
final readonly class TraitMethodUnit extends CodeUnit
{
public function isTraitMethod(): bool
{
return true;
}
}
