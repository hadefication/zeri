<?php declare(strict_types=1);








namespace SebastianBergmann\CodeUnit;

/**
@immutable
*/
final readonly class ClassUnit extends CodeUnit
{
public function isClass(): bool
{
return true;
}
}
