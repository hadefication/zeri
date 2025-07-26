<?php declare(strict_types=1);








namespace SebastianBergmann\CodeUnit;

/**
@immutable
*/
final readonly class ClassMethodUnit extends CodeUnit
{
public function isClassMethod(): bool
{
return true;
}
}
