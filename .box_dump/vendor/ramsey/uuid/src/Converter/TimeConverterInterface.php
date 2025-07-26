<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Converter;

use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Time;

/**
@immutable


*/
interface TimeConverterInterface
{
/**
@pure










*/
public function calculateTime(string $seconds, string $microseconds): Hexadecimal;

/**
@pure







*/
public function convertTime(Hexadecimal $uuidTimestamp): Time;
}
