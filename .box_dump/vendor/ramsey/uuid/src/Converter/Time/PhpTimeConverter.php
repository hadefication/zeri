<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;

use function count;
use function dechex;
use function explode;
use function is_float;
use function is_int;
use function str_pad;
use function strlen;
use function substr;

use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;

/**
@immutable



*/
class PhpTimeConverter implements TimeConverterInterface
{



private const GREGORIAN_TO_UNIX_INTERVALS = 0x01b21dd213814000;




private const SECOND_INTERVALS = 10_000_000;




private const MICROSECOND_INTERVALS = 10;

private int $phpPrecision;
private CalculatorInterface $calculator;
private TimeConverterInterface $fallbackConverter;

public function __construct(
?CalculatorInterface $calculator = null,
?TimeConverterInterface $fallbackConverter = null,
) {
if ($calculator === null) {
$calculator = new BrickMathCalculator();
}

if ($fallbackConverter === null) {
$fallbackConverter = new GenericTimeConverter($calculator);
}

$this->calculator = $calculator;
$this->fallbackConverter = $fallbackConverter;
$this->phpPrecision = (int) ini_get('precision');
}

public function calculateTime(string $seconds, string $microseconds): Hexadecimal
{
$seconds = new IntegerObject($seconds); /**
@phpstan-ignore */
$microseconds = new IntegerObject($microseconds); /**
@phpstan-ignore */



$uuidTime = ((int) $seconds->toString() * self::SECOND_INTERVALS)
+ ((int) $microseconds->toString() * self::MICROSECOND_INTERVALS)
+ self::GREGORIAN_TO_UNIX_INTERVALS;




if (!is_int($uuidTime)) {
return $this->fallbackConverter->calculateTime(
$seconds->toString(),
$microseconds->toString(),
);
}

/**
@phpstan-ignore */
return new Hexadecimal(
str_pad(dechex($uuidTime), 16, '0', STR_PAD_LEFT)
);
}

public function convertTime(Hexadecimal $uuidTimestamp): Time
{
$timestamp = $this->calculator->toInteger($uuidTimestamp);


$splitTime = $this->splitTime(
((int) $timestamp->toString() - self::GREGORIAN_TO_UNIX_INTERVALS) / self::SECOND_INTERVALS,
);

if (count($splitTime) === 0) {
return $this->fallbackConverter->convertTime($uuidTimestamp);
}

/**
@phpstan-ignore */
return new Time($splitTime['sec'], $splitTime['usec']);
}

/**
@pure




*/
private function splitTime(float | int $time): array
{
$split = explode('.', (string) $time, 2);



if (is_float($time) && count($split) === 1) {
return [];
}

if (count($split) === 1) {
return ['sec' => $split[0], 'usec' => '0'];
}




if (strlen($split[1]) < 6 && strlen((string) $time) >= $this->phpPrecision) {
return [];
}

$microseconds = $split[1];



if (strlen($microseconds) > 6) {
$roundingDigit = (int) substr($microseconds, 6, 1);
$microseconds = (int) substr($microseconds, 0, 6);

if ($roundingDigit >= 5) {
$microseconds++;
}
}

return [
'sec' => $split[0],
'usec' => str_pad((string) $microseconds, 6, '0', STR_PAD_RIGHT),
];
}
}
