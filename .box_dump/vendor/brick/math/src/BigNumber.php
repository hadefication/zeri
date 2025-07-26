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
abstract class BigNumber implements \JsonSerializable
{



private const PARSE_REGEXP_NUMERICAL =
'/^' .
'(?<sign>[\-\+])?' .
'(?<integral>[0-9]+)?' .
'(?<point>\.)?' .
'(?<fractional>[0-9]+)?' .
'(?:[eE](?<exponent>[\-\+]?[0-9]+))?' .
'$/';




private const PARSE_REGEXP_RATIONAL =
'/^' .
'(?<sign>[\-\+])?' .
'(?<numerator>[0-9]+)' .
'\/?' .
'(?<denominator>[0-9]+)' .
'$/';

/**
@psalm-pure















*/
final public static function of(BigNumber|int|float|string $value) : static
{
$value = self::_of($value);

if (static::class === BigNumber::class) {

assert($value instanceof static);

return $value;
}

return static::from($value);
}

/**
@psalm-pure



*/
private static function _of(BigNumber|int|float|string $value) : BigNumber
{
if ($value instanceof BigNumber) {
return $value;
}

if (\is_int($value)) {
return new BigInteger((string) $value);
}

if (is_float($value)) {
$value = (string) $value;
}

if (str_contains($value, '/')) {

if (\preg_match(self::PARSE_REGEXP_RATIONAL, $value, $matches, PREG_UNMATCHED_AS_NULL) !== 1) {
throw NumberFormatException::invalidFormat($value);
}

$sign = $matches['sign'];
$numerator = $matches['numerator'];
$denominator = $matches['denominator'];

assert($numerator !== null);
assert($denominator !== null);

$numerator = self::cleanUp($sign, $numerator);
$denominator = self::cleanUp(null, $denominator);

if ($denominator === '0') {
throw DivisionByZeroException::denominatorMustNotBeZero();
}

return new BigRational(
new BigInteger($numerator),
new BigInteger($denominator),
false
);
} else {

if (\preg_match(self::PARSE_REGEXP_NUMERICAL, $value, $matches, PREG_UNMATCHED_AS_NULL) !== 1) {
throw NumberFormatException::invalidFormat($value);
}

$sign = $matches['sign'];
$point = $matches['point'];
$integral = $matches['integral'];
$fractional = $matches['fractional'];
$exponent = $matches['exponent'];

if ($integral === null && $fractional === null) {
throw NumberFormatException::invalidFormat($value);
}

if ($integral === null) {
$integral = '0';
}

if ($point !== null || $exponent !== null) {
$fractional = ($fractional ?? '');
$exponent = ($exponent !== null) ? (int)$exponent : 0;

if ($exponent === PHP_INT_MIN || $exponent === PHP_INT_MAX) {
throw new NumberFormatException('Exponent too large.');
}

$unscaledValue = self::cleanUp($sign, $integral . $fractional);

$scale = \strlen($fractional) - $exponent;

if ($scale < 0) {
if ($unscaledValue !== '0') {
$unscaledValue .= \str_repeat('0', -$scale);
}
$scale = 0;
}

return new BigDecimal($unscaledValue, $scale);
}

$integral = self::cleanUp($sign, $integral);

return new BigInteger($integral);
}
}

/**
@psalm-pure




*/
abstract protected static function from(BigNumber $number): static;

/**
@psalm-pure



*/
final protected function newBigInteger(string $value) : BigInteger
{
return new BigInteger($value);
}

/**
@psalm-pure



*/
final protected function newBigDecimal(string $value, int $scale = 0) : BigDecimal
{
return new BigDecimal($value, $scale);
}

/**
@psalm-pure



*/
final protected function newBigRational(BigInteger $numerator, BigInteger $denominator, bool $checkDenominator) : BigRational
{
return new BigRational($numerator, $denominator, $checkDenominator);
}

/**
@psalm-pure








*/
final public static function min(BigNumber|int|float|string ...$values) : static
{
$min = null;

foreach ($values as $value) {
$value = static::of($value);

if ($min === null || $value->isLessThan($min)) {
$min = $value;
}
}

if ($min === null) {
throw new \InvalidArgumentException(__METHOD__ . '() expects at least one value.');
}

return $min;
}

/**
@psalm-pure








*/
final public static function max(BigNumber|int|float|string ...$values) : static
{
$max = null;

foreach ($values as $value) {
$value = static::of($value);

if ($max === null || $value->isGreaterThan($max)) {
$max = $value;
}
}

if ($max === null) {
throw new \InvalidArgumentException(__METHOD__ . '() expects at least one value.');
}

return $max;
}

/**
@psalm-pure








*/
final public static function sum(BigNumber|int|float|string ...$values) : static
{

$sum = null;

foreach ($values as $value) {
$value = static::of($value);

$sum = $sum === null ? $value : self::add($sum, $value);
}

if ($sum === null) {
throw new \InvalidArgumentException(__METHOD__ . '() expects at least one value.');
}

return $sum;
}

/**
@psalm-pure







*/
private static function add(BigNumber $a, BigNumber $b) : BigNumber
{
if ($a instanceof BigRational) {
return $a->plus($b);
}

if ($b instanceof BigRational) {
return $b->plus($a);
}

if ($a instanceof BigDecimal) {
return $a->plus($b);
}

if ($b instanceof BigDecimal) {
return $b->plus($a);
}



return $a->plus($b);
}

/**
@psalm-pure





*/
private static function cleanUp(string|null $sign, string $number) : string
{
$number = \ltrim($number, '0');

if ($number === '') {
return '0';
}

return $sign === '-' ? '-' . $number : $number;
}




final public function isEqualTo(BigNumber|int|float|string $that) : bool
{
return $this->compareTo($that) === 0;
}




final public function isLessThan(BigNumber|int|float|string $that) : bool
{
return $this->compareTo($that) < 0;
}




final public function isLessThanOrEqualTo(BigNumber|int|float|string $that) : bool
{
return $this->compareTo($that) <= 0;
}




final public function isGreaterThan(BigNumber|int|float|string $that) : bool
{
return $this->compareTo($that) > 0;
}




final public function isGreaterThanOrEqualTo(BigNumber|int|float|string $that) : bool
{
return $this->compareTo($that) >= 0;
}




final public function isZero() : bool
{
return $this->getSign() === 0;
}




final public function isNegative() : bool
{
return $this->getSign() < 0;
}




final public function isNegativeOrZero() : bool
{
return $this->getSign() <= 0;
}




final public function isPositive() : bool
{
return $this->getSign() > 0;
}




final public function isPositiveOrZero() : bool
{
return $this->getSign() >= 0;
}

/**
@psalm-return




*/
abstract public function getSign() : int;

/**
@psalm-return






*/
abstract public function compareTo(BigNumber|int|float|string $that) : int;






abstract public function toBigInteger() : BigInteger;






abstract public function toBigDecimal() : BigDecimal;




abstract public function toBigRational() : BigRational;










abstract public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::UNNECESSARY) : BigDecimal;









abstract public function toInt() : int;










abstract public function toFloat() : float;







abstract public function __toString() : string;

#[Override]
final public function jsonSerialize() : string
{
return $this->__toString();
}
}
