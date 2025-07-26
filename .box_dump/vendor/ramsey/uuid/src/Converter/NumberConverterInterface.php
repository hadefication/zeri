<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Converter;

/**
@immutable


*/
interface NumberConverterInterface
{
/**
@pure









*/
public function fromHex(string $hex): string;

/**
@pure







*/
public function toHex(string $number): string;
}
