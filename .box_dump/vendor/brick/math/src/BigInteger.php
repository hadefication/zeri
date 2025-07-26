<?php

declare(strict_types=1);

namespace Brick\Math;

use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\IntegerOverflowException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NegativeNumberException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Internal\Calculator;
use Override;

/**
@psalm-immutable





*/
final class BigInteger extends BigNumber
{






private readonly string $value;






protected function __construct(string $value)
{
$this->value = $value;
}

/**
@psalm-pure
*/
#[Override]
protected static function from(BigNumber $number): static
{
return $number->toBigInteger();
}

/**
@psalm-pure
















*/
public static function fromBase(string $number, int $base) : BigInteger
{
if ($number === '') {
throw new NumberFormatException('The number cannot be empty.');
}

if ($base < 2 || $base > 36) {
throw new \InvalidArgumentException(\sprintf('Base %d is not in range 2 to 36.', $base));
}

if ($number[0] === '-') {
$sign = '-';
$number = \substr($number, 1);
} elseif ($number[0] === '+') {
$sign = '';
$number = \substr($number, 1);
} else {
$sign = '';
}

if ($number === '') {
throw new NumberFormatException('The number cannot be empty.');
}

$number = \ltrim($number, '0');

if ($number === '') {

return BigInteger::zero();
}

if ($number === '1') {

return new BigInteger($sign . '1');
}

$pattern = '/[^' . \substr(Calculator::ALPHABET, 0, $base) . ']/';

if (\preg_match($pattern, \strtolower($number), $matches) === 1) {
throw new NumberFormatException(\sprintf('"%s" is not a valid character in base %d.', $matches[0], $base));
}

if ($base === 10) {

return new BigInteger($sign . $number);
}

$result = Calculator::get()->fromBase($number, $base);

return new BigInteger($sign . $result);
}

/**
@psalm-pure










*/
public static function fromArbitraryBase(string $number, string $alphabet) : BigInteger
{
if ($number === '') {
throw new NumberFormatException('The number cannot be empty.');
}

$base = \strlen($alphabet);

if ($base < 2) {
throw new \InvalidArgumentException('The alphabet must contain at least 2 chars.');
}

$pattern = '/[^' . \preg_quote($alphabet, '/') . ']/';

if (\preg_match($pattern, $number, $matches) === 1) {
throw NumberFormatException::charNotInAlphabet($matches[0]);
}

$number = Calculator::get()->fromArbitraryBase($number, $alphabet, $base);

return new BigInteger($number);
}


















public static function fromBytes(string $value, bool $signed = true) : BigInteger
{
if ($value === '') {
throw new NumberFormatException('The byte string must not be empty.');
}

$twosComplement = false;

if ($signed) {
$x = \ord($value[0]);

if (($twosComplement = ($x >= 0x80))) {
$value = ~$value;
}
}

$number = self::fromBase(\bin2hex($value), 16);

if ($twosComplement) {
return $number->plus(1)->negated();
}

return $number;
}

/**
@psalm-param(callable(int): string)|null $randomBytesGenerator











*/
public static function randomBits(int $numBits, ?callable $randomBytesGenerator = null) : BigInteger
{
if ($numBits < 0) {
throw new \InvalidArgumentException('The number of bits cannot be negative.');
}

if ($numBits === 0) {
return BigInteger::zero();
}

if ($randomBytesGenerator === null) {
$randomBytesGenerator = random_bytes(...);
}


$byteLength = \intdiv($numBits - 1, 8) + 1;

$extraBits = ($byteLength * 8 - $numBits);
$bitmask = \chr(0xFF >> $extraBits);

$randomBytes = $randomBytesGenerator($byteLength);
$randomBytes[0] = $randomBytes[0] & $bitmask;

return self::fromBytes($randomBytes, false);
}

/**
@psalm-param(callable(int): string)|null $randomBytesGenerator













*/
public static function randomRange(
BigNumber|int|float|string $min,
BigNumber|int|float|string $max,
?callable $randomBytesGenerator = null
) : BigInteger {
$min = BigInteger::of($min);
$max = BigInteger::of($max);

if ($min->isGreaterThan($max)) {
throw new MathException('$min cannot be greater than $max.');
}

if ($min->isEqualTo($max)) {
return $min;
}

$diff = $max->minus($min);
$bitLength = $diff->getBitLength();


do {
$randomNumber = self::randomBits($bitLength, $randomBytesGenerator);
} while ($randomNumber->isGreaterThan($diff));

return $randomNumber->plus($min);
}

/**
@psalm-pure


*/
public static function zero() : BigInteger
{
/**
@psalm-suppress

*/
static $zero;

if ($zero === null) {
$zero = new BigInteger('0');
}

return $zero;
}

/**
@psalm-pure


*/
public static function one() : BigInteger
{
/**
@psalm-suppress

*/
static $one;

if ($one === null) {
$one = new BigInteger('1');
}

return $one;
}

/**
@psalm-pure


*/
public static function ten() : BigInteger
{
/**
@psalm-suppress

*/
static $ten;

if ($ten === null) {
$ten = new BigInteger('10');
}

return $ten;
}

public static function gcdMultiple(BigInteger $a, BigInteger ...$n): BigInteger
{
$result = $a;

foreach ($n as $next) {
$result = $result->gcd($next);

if ($result->isEqualTo(1)) {
return $result;
}
}

return $result;
}








public function plus(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

if ($that->value === '0') {
return $this;
}

if ($this->value === '0') {
return $that;
}

$value = Calculator::get()->add($this->value, $that->value);

return new BigInteger($value);
}








public function minus(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

if ($that->value === '0') {
return $this;
}

$value = Calculator::get()->sub($this->value, $that->value);

return new BigInteger($value);
}








public function multipliedBy(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

if ($that->value === '1') {
return $this;
}

if ($this->value === '1') {
return $that;
}

$value = Calculator::get()->mul($this->value, $that->value);

return new BigInteger($value);
}










public function dividedBy(BigNumber|int|float|string $that, RoundingMode $roundingMode = RoundingMode::UNNECESSARY) : BigInteger
{
$that = BigInteger::of($that);

if ($that->value === '1') {
return $this;
}

if ($that->value === '0') {
throw DivisionByZeroException::divisionByZero();
}

$result = Calculator::get()->divRound($this->value, $that->value, $roundingMode);

return new BigInteger($result);
}






public function power(int $exponent) : BigInteger
{
if ($exponent === 0) {
return BigInteger::one();
}

if ($exponent === 1) {
return $this;
}

if ($exponent < 0 || $exponent > Calculator::MAX_POWER) {
throw new \InvalidArgumentException(\sprintf(
'The exponent %d is not in the range 0 to %d.',
$exponent,
Calculator::MAX_POWER
));
}

return new BigInteger(Calculator::get()->pow($this->value, $exponent));
}








public function quotient(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

if ($that->value === '1') {
return $this;
}

if ($that->value === '0') {
throw DivisionByZeroException::divisionByZero();
}

$quotient = Calculator::get()->divQ($this->value, $that->value);

return new BigInteger($quotient);
}










public function remainder(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

if ($that->value === '1') {
return BigInteger::zero();
}

if ($that->value === '0') {
throw DivisionByZeroException::divisionByZero();
}

$remainder = Calculator::get()->divR($this->value, $that->value);

return new BigInteger($remainder);
}

/**
@psalm-return








*/
public function quotientAndRemainder(BigNumber|int|float|string $that) : array
{
$that = BigInteger::of($that);

if ($that->value === '0') {
throw DivisionByZeroException::divisionByZero();
}

[$quotient, $remainder] = Calculator::get()->divQR($this->value, $that->value);

return [
new BigInteger($quotient),
new BigInteger($remainder)
];
}













public function mod(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

if ($that->value === '0') {
throw DivisionByZeroException::modulusMustNotBeZero();
}

$value = Calculator::get()->mod($this->value, $that->value);

return new BigInteger($value);
}









public function modInverse(BigInteger $m) : BigInteger
{
if ($m->value === '0') {
throw DivisionByZeroException::modulusMustNotBeZero();
}

if ($m->isNegative()) {
throw new NegativeNumberException('Modulus must not be negative.');
}

if ($m->value === '1') {
return BigInteger::zero();
}

$value = Calculator::get()->modInverse($this->value, $m->value);

if ($value === null) {
throw new MathException('Unable to compute the modInverse for the given modulus.');
}

return new BigInteger($value);
}












public function modPow(BigNumber|int|float|string $exp, BigNumber|int|float|string $mod) : BigInteger
{
$exp = BigInteger::of($exp);
$mod = BigInteger::of($mod);

if ($this->isNegative() || $exp->isNegative() || $mod->isNegative()) {
throw new NegativeNumberException('The operands cannot be negative.');
}

if ($mod->isZero()) {
throw DivisionByZeroException::modulusMustNotBeZero();
}

$result = Calculator::get()->modPow($this->value, $exp->value, $mod->value);

return new BigInteger($result);
}








public function gcd(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

if ($that->value === '0' && $this->value[0] !== '-') {
return $this;
}

if ($this->value === '0' && $that->value[0] !== '-') {
return $that;
}

$value = Calculator::get()->gcd($this->value, $that->value);

return new BigInteger($value);
}








public function sqrt() : BigInteger
{
if ($this->value[0] === '-') {
throw new NegativeNumberException('Cannot calculate the square root of a negative number.');
}

$value = Calculator::get()->sqrt($this->value);

return new BigInteger($value);
}




public function abs() : BigInteger
{
return $this->isNegative() ? $this->negated() : $this;
}




public function negated() : BigInteger
{
return new BigInteger(Calculator::get()->neg($this->value));
}








public function and(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

return new BigInteger(Calculator::get()->and($this->value, $that->value));
}








public function or(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

return new BigInteger(Calculator::get()->or($this->value, $that->value));
}








public function xor(BigNumber|int|float|string $that) : BigInteger
{
$that = BigInteger::of($that);

return new BigInteger(Calculator::get()->xor($this->value, $that->value));
}




public function not() : BigInteger
{
return $this->negated()->minus(1);
}




public function shiftedLeft(int $distance) : BigInteger
{
if ($distance === 0) {
return $this;
}

if ($distance < 0) {
return $this->shiftedRight(- $distance);
}

return $this->multipliedBy(BigInteger::of(2)->power($distance));
}




public function shiftedRight(int $distance) : BigInteger
{
if ($distance === 0) {
return $this;
}

if ($distance < 0) {
return $this->shiftedLeft(- $distance);
}

$operand = BigInteger::of(2)->power($distance);

if ($this->isPositiveOrZero()) {
return $this->quotient($operand);
}

return $this->dividedBy($operand, RoundingMode::UP);
}







public function getBitLength() : int
{
if ($this->value === '0') {
return 0;
}

if ($this->isNegative()) {
return $this->abs()->minus(1)->getBitLength();
}

return \strlen($this->toBase(2));
}






public function getLowestSetBit() : int
{
$n = $this;
$bitLength = $this->getBitLength();

for ($i = 0; $i <= $bitLength; $i++) {
if ($n->isOdd()) {
return $i;
}

$n = $n->shiftedRight(1);
}

return -1;
}




public function isEven() : bool
{
return \in_array($this->value[-1], ['0', '2', '4', '6', '8'], true);
}




public function isOdd() : bool
{
return \in_array($this->value[-1], ['1', '3', '5', '7', '9'], true);
}










public function testBit(int $n) : bool
{
if ($n < 0) {
throw new \InvalidArgumentException('The bit to test cannot be negative.');
}

return $this->shiftedRight($n)->isOdd();
}

#[Override]
public function compareTo(BigNumber|int|float|string $that) : int
{
$that = BigNumber::of($that);

if ($that instanceof BigInteger) {
return Calculator::get()->cmp($this->value, $that->value);
}

return - $that->compareTo($this);
}

#[Override]
public function getSign() : int
{
return ($this->value === '0') ? 0 : (($this->value[0] === '-') ? -1 : 1);
}

#[Override]
public function toBigInteger() : BigInteger
{
return $this;
}

#[Override]
public function toBigDecimal() : BigDecimal
{
return self::newBigDecimal($this->value);
}

#[Override]
public function toBigRational() : BigRational
{
return self::newBigRational($this, BigInteger::one(), false);
}

#[Override]
public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::UNNECESSARY) : BigDecimal
{
return $this->toBigDecimal()->toScale($scale, $roundingMode);
}

#[Override]
public function toInt() : int
{
$intValue = (int) $this->value;

if ($this->value !== (string) $intValue) {
throw IntegerOverflowException::toIntOverflow($this);
}

return $intValue;
}

#[Override]
public function toFloat() : float
{
return (float) $this->value;
}








public function toBase(int $base) : string
{
if ($base === 10) {
return $this->value;
}

if ($base < 2 || $base > 36) {
throw new \InvalidArgumentException(\sprintf('Base %d is out of range [2, 36]', $base));
}

return Calculator::get()->toBase($this->value, $base);
}












public function toArbitraryBase(string $alphabet) : string
{
$base = \strlen($alphabet);

if ($base < 2) {
throw new \InvalidArgumentException('The alphabet must contain at least 2 chars.');
}

if ($this->value[0] === '-') {
throw new NegativeNumberException(__FUNCTION__ . '() does not support negative numbers.');
}

return Calculator::get()->toArbitraryBase($this->value, $alphabet, $base);
}



















public function toBytes(bool $signed = true) : string
{
if (! $signed && $this->isNegative()) {
throw new NegativeNumberException('Cannot convert a negative number to a byte string when $signed is false.');
}

$hex = $this->abs()->toBase(16);

if (\strlen($hex) % 2 !== 0) {
$hex = '0' . $hex;
}

$baseHexLength = \strlen($hex);

if ($signed) {
if ($this->isNegative()) {
$bin = \hex2bin($hex);
assert($bin !== false);

$hex = \bin2hex(~$bin);
$hex = self::fromBase($hex, 16)->plus(1)->toBase(16);

$hexLength = \strlen($hex);

if ($hexLength < $baseHexLength) {
$hex = \str_repeat('0', $baseHexLength - $hexLength) . $hex;
}

if ($hex[0] < '8') {
$hex = 'FF' . $hex;
}
} else {
if ($hex[0] >= '8') {
$hex = '00' . $hex;
}
}
}

return \hex2bin($hex);
}




#[Override]
public function __toString() : string
{

return $this->value;
}








public function __serialize(): array
{
return ['value' => $this->value];
}

/**
@psalm-suppress







*/
public function __unserialize(array $data): void
{
if (isset($this->value)) {
throw new \LogicException('__unserialize() is an internal function, it must not be called directly.');
}

$this->value = $data['value'];
}
}
