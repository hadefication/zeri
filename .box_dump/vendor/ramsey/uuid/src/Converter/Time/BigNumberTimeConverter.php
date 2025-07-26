<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Converter\Time;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;

/**
@immutable





*/
class BigNumberTimeConverter implements TimeConverterInterface
{
private TimeConverterInterface $converter;

public function __construct()
{
$this->converter = new GenericTimeConverter(new BrickMathCalculator());
}

public function calculateTime(string $seconds, string $microseconds): Hexadecimal
{
return $this->converter->calculateTime($seconds, $microseconds);
}

public function convertTime(Hexadecimal $uuidTimestamp): Time
{
return $this->converter->convertTime($uuidTimestamp);
}
}
