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
class UnixTimeConverter implements TimeConverterInterface
{
private const MILLISECONDS = 1000;

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
new IntegerObject(self::MILLISECONDS) /**
@phpstan-ignore */
);


$usec = $this->calculator->divide(
RoundingMode::DOWN, 
0,
$timestamp->getMicroseconds(),
new IntegerObject(self::MILLISECONDS), /**
@phpstan-ignore */
);


$unixTime = $this->calculator->add($sec, $usec);

/**
@phpstan-ignore */
return new Hexadecimal(
str_pad(
$this->calculator->toHexadecimal($unixTime)->toString(),
12,
'0',
STR_PAD_LEFT
),
);
}

public function convertTime(Hexadecimal $uuidTimestamp): Time
{
$milliseconds = $this->calculator->toInteger($uuidTimestamp);

$unixTimestamp = $this->calculator->divide(
RoundingMode::HALF_UP,
6,
$milliseconds,
new IntegerObject(self::MILLISECONDS), /**
@phpstan-ignore */
);

$split = explode('.', (string) $unixTimestamp, 2);

/**
@phpstan-ignore */
return new Time($split[0], $split[1] ?? '0');
}
}
