<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Converter\Number;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Math\BrickMathCalculator;

/**
@immutable





*/
class BigNumberConverter implements NumberConverterInterface
{
private NumberConverterInterface $converter;

public function __construct()
{
$this->converter = new GenericNumberConverter(new BrickMathCalculator());
}

/**
@pure
*/
public function fromHex(string $hex): string
{
return $this->converter->fromHex($hex);
}

/**
@pure
*/
public function toHex(string $number): string
{
return $this->converter->toHex($number);
}
}
