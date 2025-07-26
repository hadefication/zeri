<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Math;

use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\NumberInterface;

/**
@immutable


*/
interface CalculatorInterface
{
/**
@pure







*/
public function add(NumberInterface $augend, NumberInterface ...$addends): NumberInterface;

/**
@pure







*/
public function subtract(NumberInterface $minuend, NumberInterface ...$subtrahends): NumberInterface;

/**
@pure







*/
public function multiply(NumberInterface $multiplicand, NumberInterface ...$multipliers): NumberInterface;

/**
@pure










*/
public function divide(
int $roundingMode,
int $scale,
NumberInterface $dividend,
NumberInterface ...$divisors,
): NumberInterface;

/**
@pure







*/
public function fromBase(string $value, int $base): IntegerObject;

/**
@pure







*/
public function toBase(IntegerObject $value, int $base): string;

/**
@pure


*/
public function toHexadecimal(IntegerObject $value): Hexadecimal;

/**
@pure


*/
public function toInteger(Hexadecimal $value): IntegerObject;
}
