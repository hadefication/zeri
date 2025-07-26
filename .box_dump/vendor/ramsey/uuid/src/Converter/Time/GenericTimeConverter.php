<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Math\CalculatorInterface;
use Ramsey\Uuid\Math\RoundingMode;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\Time;

use function explode;
use function str_pad;

use const STR_PAD_LEFT;

/**
@immutable


*/
class GenericTimeConverter implements TimeConverterInterface
{



private const GREGORIAN_TO_UNIX_INTERVALS = '122192928000000000';




private const SECOND_INTERVALS = '10000000';




private const MICROSECOND_INTERVALS = '10';

public function __construct(private CalculatorInterface $calculator)
{
}

public function calculateTime(string $seconds, string $microseconds): Hexadecimal
{
/**
@phpstan-ignore */
$timestamp = new Time($seconds, $microseconds);


$sec = $this->calculator->multiply(
$timestamp->getSeconds(),
new IntegerObject(self::SECOND_INTERVALS), /**
@phpstan-ignore */
);


$usec = $this->calculator->multiply(
$timestamp->getMicroseconds(),
new IntegerObject(self::MICROSECOND_INTERVALS), /**
@phpstan-ignore */
);

/**
@phpstan-ignore





*/
$uuidTime = $this->calculator->add($sec, $usec, new IntegerObject(self::GREGORIAN_TO_UNIX_INTERVALS));

/**
@phpstan-ignore


*/
return new Hexadecimal(str_pad($this->calculator->toHexadecimal($uuidTime)->toString(), 16, '0', STR_PAD_LEFT));
}

public function convertTime(Hexadecimal $uuidTimestamp): Time
{


$epochNanoseconds = $this->calculator->subtract(
$this->calculator->toInteger($uuidTimestamp),
new IntegerObject(self::GREGORIAN_TO_UNIX_INTERVALS), /**
@phpstan-ignore */
);


$unixTimestamp = $this->calculator->divide(
RoundingMode::HALF_UP,
6,
$epochNanoseconds,
new IntegerObject(self::SECOND_INTERVALS), /**
@phpstan-ignore */
);

$split = explode('.', (string) $unixTimestamp, 2);

/**
@phpstan-ignore */
return new Time($split[0], $split[1] ?? 0);
}
}
