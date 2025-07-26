<?php declare(strict_types=1);








namespace SebastianBergmann\CodeUnit;

/**
@immutable
*/
final readonly class InterfaceMethodUnit extends CodeUnit
{
public function isInterfaceMethod(): bool
{
return true;
}
}
