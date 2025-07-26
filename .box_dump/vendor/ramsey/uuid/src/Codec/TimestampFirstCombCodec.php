<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Codec;

use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\UuidInterface;

use function bin2hex;
use function sprintf;
use function substr;
use function substr_replace;

/**
@immutable























*/
class TimestampFirstCombCodec extends StringCodec
{



public function encode(UuidInterface $uuid): string
{
/**
@phpstan-ignore */
$bytes = $this->swapBytes($uuid->getFields()->getBytes());

return sprintf(
'%08s-%04s-%04s-%04s-%012s',
bin2hex(substr($bytes, 0, 4)),
bin2hex(substr($bytes, 4, 2)),
bin2hex(substr($bytes, 6, 2)),
bin2hex(substr($bytes, 8, 2)),
bin2hex(substr($bytes, 10))
);
}




public function encodeBinary(UuidInterface $uuid): string
{
/**
@phpstan-ignore-next-line */
return $this->swapBytes($uuid->getFields()->getBytes());
}






public function decode(string $encodedUuid): UuidInterface
{
/**
@phpstan-ignore */
$bytes = $this->getBytes($encodedUuid);

/**
@phpstan-ignore */
return $this->getBuilder()->build($this, $this->swapBytes($bytes));
}

public function decodeBytes(string $bytes): UuidInterface
{
/**
@phpstan-ignore */
return $this->getBuilder()->build($this, $this->swapBytes($bytes));
}

/**
@pure


*/
private function swapBytes(string $bytes): string
{
$first48Bits = substr($bytes, 0, 6);
$last48Bits = substr($bytes, -6);

return substr_replace(substr_replace($bytes, $last48Bits, 0, 6), $first48Bits, -6);
}
}
