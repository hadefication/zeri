<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Converter\Number;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Type\Integer as IntegerObject;

/**
@immutable


*/
class GenericNumberConverter implements NumberConverterInterface
{
public function __construct(private CalculatorInterface $calculator)
{
}

/**
@pure
*/
public function fromHex(string $hex): string
{
return $this->calculator->fromBase($hex, 16)->toString();
}

/**
@pure
*/
public function toHex(string $number): string
{
/**
@phpstan-ignore */
return $this->calculator->toBase(new IntegerObject($number), 16);
}
}
