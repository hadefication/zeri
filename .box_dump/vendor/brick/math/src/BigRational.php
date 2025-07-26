<?php

declare(strict_types=1);

namespace Brick\Math;

use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Override;

/**
@psalm-immutable




*/
final class BigRational extends BigNumber
{



private readonly BigInteger $numerator;




private readonly BigInteger $denominator;










protected function __construct(BigInteger $numerator, BigInteger $denominator, bool $checkDenominator)
{
if ($checkDenominator) {
if ($denominator->isZero()) {
throw DivisionByZeroException::denominatorMustNotBeZero();
}

if ($denominator->isNegative()) {
$numerator = $numerator->negated();
$denominator = $denominator->negated();
}
}

$this->numerator = $numerator;
$this->denominator = $denominator;
}

/**
@psalm-pure
*/
#[Override]
protected static function from(BigNumber $number): static
{
return $number->toBigRational();
}

/**
@psalm-pure












*/
public static function nd(
BigNumber|int|float|string $numerator,
BigNumber|int|float|string $denominator,
) : BigRational {
$numerator = BigInteger::of($numerator);
$denominator = BigInteger::of($denominator);

return new BigRational($numerator, $denominator, true);
}

/**
@psalm-pure


*/
public static function zero() : BigRational
{
/**
@psalm-suppress

*/
static $zero;

if ($zero === null) {
$zero = new BigRational(BigInteger::zero(), BigInteger::one(), false);
}

return $zero;
}

/**
@psalm-pure


*/
public static function one() : BigRational
{
/**
@psalm-suppress

*/
static $one;

if ($one === null) {
$one = new BigRational(BigInteger::one(), BigInteger::one(), false);
}

return $one;
}

/**
@psalm-pure


*/
public static function ten() : BigRational
{
/**
@psalm-suppress

*/
static $ten;

if ($ten === null) {
$ten = new BigRational(BigInteger::ten(), BigInteger::one(), false);
}

return $ten;
}

public function getNumerator() : BigInteger
{
return $this->numerator;
}

public function getDenominator() : BigInteger
{
return $this->denominator;
}




public function quotient() : BigInteger
{
return $this->numerator->quotient($this->denominator);
}




public function remainder() : BigInteger
{
return $this->numerator->remainder($this->denominator);
}

/**
@psalm-return




*/
public function quotientAndRemainder() : array
{
return $this->numerator->quotientAndRemainder($this->denominator);
}








public function plus(BigNumber|int|float|string $that) : BigRational
{
$that = BigRational::of($that);

$numerator = $this->numerator->multipliedBy($that->denominator);
$numerator = $numerator->plus($that->numerator->multipliedBy($this->denominator));
$denominator = $this->denominator->multipliedBy($that->denominator);

return new BigRational($numerator, $denominator, false);
}








public function minus(BigNumber|int|float|string $that) : BigRational
{
$that = BigRational::of($that);

$numerator = $this->numerator->multipliedBy($that->denominator);
$numerator = $numerator->minus($that->numerator->multipliedBy($this->denominator));
$denominator = $this->denominator->multipliedBy($that->denominator);

return new BigRational($numerator, $denominator, false);
}








public function multipliedBy(BigNumber|int|float|string $that) : BigRational
{
$that = BigRational::of($that);

$numerator = $this->numerator->multipliedBy($that->numerator);
$denominator = $this->denominator->multipliedBy($that->denominator);

return new BigRational($numerator, $denominator, false);
}








public function dividedBy(BigNumber|int|float|string $that) : BigRational
{
$that = BigRational::of($that);

$numerator = $this->numerator->multipliedBy($that->denominator);
$denominator = $this->denominator->multipliedBy($that->numerator);

return new BigRational($numerator, $denominator, true);
}






public function power(int $exponent) : BigRational
{
if ($exponent === 0) {
$one = BigInteger::one();

return new BigRational($one, $one, false);
}

if ($exponent === 1) {
return $this;
}

return new BigRational(
$this->numerator->power($exponent),
$this->denominator->power($exponent),
false
);
}








public function reciprocal() : BigRational
{
return new BigRational($this->denominator, $this->numerator, true);
}




public function abs() : BigRational
{
return new BigRational($this->numerator->abs(), $this->denominator, false);
}




public function negated() : BigRational
{
return new BigRational($this->numerator->negated(), $this->denominator, false);
}




public function simplified() : BigRational
{
$gcd = $this->numerator->gcd($this->denominator);

$numerator = $this->numerator->quotient($gcd);
$denominator = $this->denominator->quotient($gcd);

return new BigRational($numerator, $denominator, false);
}

#[Override]
public function compareTo(BigNumber|int|float|string $that) : int
{
return $this->minus($that)->getSign();
}

#[Override]
public function getSign() : int
{
return $this->numerator->getSign();
}

#[Override]
public function toBigInteger() : BigInteger
{
$simplified = $this->simplified();

if (! $simplified->denominator->isEqualTo(1)) {
throw new RoundingNecessaryException('This rational number cannot be represented as an integer value without rounding.');
}

return $simplified->numerator;
}

#[Override]
public function toBigDecimal() : BigDecimal
{
return $this->numerator->toBigDecimal()->exactlyDividedBy($this->denominator);
}

#[Override]
public function toBigRational() : BigRational
{
return $this;
}

#[Override]
public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::UNNECESSARY) : BigDecimal
{
return $this->numerator->toBigDecimal()->dividedBy($this->denominator, $scale, $roundingMode);
}

#[Override]
public function toInt() : int
{
return $this->toBigInteger()->toInt();
}

#[Override]
public function toFloat() : float
{
$simplified = $this->simplified();
return $simplified->numerator->toFloat() / $simplified->denominator->toFloat();
}

#[Override]
public function __toString() : string
{
$numerator = (string) $this->numerator;
$denominator = (string) $this->denominator;

if ($denominator === '1') {
return $numerator;
}

return $numerator . '/' . $denominator;
}








public function __serialize(): array
{
return ['numerator' => $this->numerator, 'denominator' => $this->denominator];
}

/**
@psalm-suppress







*/
public function __unserialize(array $data): void
{
if (isset($this->numerator)) {
throw new \LogicException('__unserialize() is an internal function, it must not be called directly.');
}

$this->numerator = $data['numerator'];
$this->denominator = $data['denominator'];
}
}
