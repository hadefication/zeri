<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Exception\RandomSourceException;
use Throwable;






class RandomBytesGenerator implements RandomGeneratorInterface
{





public function generate(int $length): string
{
try {
return random_bytes($length);
} catch (Throwable $exception) {
throw new RandomSourceException($exception->getMessage(), (int) $exception->getCode(), $exception);
}
}
}
