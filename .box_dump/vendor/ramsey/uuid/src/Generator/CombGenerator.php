<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;

use function bin2hex;
use function explode;
use function hex2bin;
use function microtime;
use function str_pad;
use function substr;

use const STR_PAD_LEFT;































class CombGenerator implements RandomGeneratorInterface
{
public const TIMESTAMP_BYTES = 6;

public function __construct(
private RandomGeneratorInterface $generator,
private NumberConverterInterface $numberConverter
) {
}






public function generate(int $length): string
{
if ($length < self::TIMESTAMP_BYTES) {
throw new InvalidArgumentException(
'Length must be a positive integer greater than or equal to ' . self::TIMESTAMP_BYTES
);
}

if ($length % 2 !== 0) {
throw new InvalidArgumentException('Length must be an even number');
}

$hash = '';

/**
@phpstan-ignore */
if (self::TIMESTAMP_BYTES > 0 && $length > self::TIMESTAMP_BYTES) {
$hash = $this->generator->generate($length - self::TIMESTAMP_BYTES);
}

$lsbTime = str_pad(
$this->numberConverter->toHex($this->timestamp()),
self::TIMESTAMP_BYTES * 2,
'0',
STR_PAD_LEFT,
);

return (string) hex2bin(str_pad(bin2hex($hash), $length - self::TIMESTAMP_BYTES, '0') . $lsbTime);
}




private function timestamp(): string
{
$time = explode(' ', microtime(false));

return $time[1] . substr($time[0], 2, 5);
}
}
