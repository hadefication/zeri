<?php declare(strict_types=1);








namespace SebastianBergmann\CodeUnit;

/**
@immutable
*/
final readonly class InterfaceUnit extends CodeUnit
{
public function isInterface(): bool
{
return true;
}
}
