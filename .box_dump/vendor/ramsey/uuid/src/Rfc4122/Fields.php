<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Fields\SerializableFieldsTrait;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;

use function bin2hex;
use function dechex;
use function hexdec;
use function sprintf;
use function str_pad;
use function strlen;
use function substr;
use function unpack;

use const STR_PAD_LEFT;

/**
@immutable




*/
final class Fields implements FieldsInterface
{
use MaxTrait;
use NilTrait;
use SerializableFieldsTrait;
use VariantTrait;
use VersionTrait;








public function __construct(private string $bytes)
{
if (strlen($this->bytes) !== 16) {
throw new InvalidArgumentException(
'The byte string must be 16 bytes long; ' . 'received ' . strlen($this->bytes) . ' bytes',
);
}

if (!$this->isCorrectVariant()) {
throw new InvalidArgumentException(
'The byte string received does not conform to the RFC 9562 (formerly RFC 4122) variant',
);
}

if (!$this->isCorrectVersion()) {
throw new InvalidArgumentException(
'The byte string received does not contain a valid RFC 9562 (formerly RFC 4122) version',
);
}
}

/**
@pure
*/
public function getBytes(): string
{
return $this->bytes;
}

public function getClockSeq(): Hexadecimal
{
if ($this->isMax()) {
$clockSeq = 0xffff;
} elseif ($this->isNil()) {
$clockSeq = 0x0000;
} else {
$clockSeq = hexdec(bin2hex(substr($this->bytes, 8, 2))) & 0x3fff;
}

return new Hexadecimal(str_pad(dechex($clockSeq), 4, '0', STR_PAD_LEFT));
}

public function getClockSeqHiAndReserved(): Hexadecimal
{
return new Hexadecimal(bin2hex(substr($this->bytes, 8, 1)));
}

public function getClockSeqLow(): Hexadecimal
{
return new Hexadecimal(bin2hex(substr($this->bytes, 9, 1)));
}

public function getNode(): Hexadecimal
{
return new Hexadecimal(bin2hex(substr($this->bytes, 10)));
}

public function getTimeHiAndVersion(): Hexadecimal
{
return new Hexadecimal(bin2hex(substr($this->bytes, 6, 2)));
}

public function getTimeLow(): Hexadecimal
{
return new Hexadecimal(bin2hex(substr($this->bytes, 0, 4)));
}

public function getTimeMid(): Hexadecimal
{
return new Hexadecimal(bin2hex(substr($this->bytes, 4, 2)));
}













public function getTimestamp(): Hexadecimal
{
return new Hexadecimal(match ($this->getVersion()) {
Uuid::UUID_TYPE_DCE_SECURITY => sprintf(
'%03x%04s%08s',
hexdec($this->getTimeHiAndVersion()->toString()) & 0x0fff,
$this->getTimeMid()->toString(),
''
),
Uuid::UUID_TYPE_REORDERED_TIME => sprintf(
'%08s%04s%03x',
$this->getTimeLow()->toString(),
$this->getTimeMid()->toString(),
hexdec($this->getTimeHiAndVersion()->toString()) & 0x0fff
),


Uuid::UUID_TYPE_UNIX_TIME => sprintf(
'%011s%04s',
$this->getTimeLow()->toString(),
$this->getTimeMid()->toString(),
),
default => sprintf(
'%03x%04s%08s',
hexdec($this->getTimeHiAndVersion()->toString()) & 0x0fff,
$this->getTimeMid()->toString(),
$this->getTimeLow()->toString()
),
});
}

public function getVersion(): ?int
{
if ($this->isNil() || $this->isMax()) {
return null;
}


$parts = unpack('n*', $this->bytes);

return $parts[4] >> 12;
}

private function isCorrectVariant(): bool
{
if ($this->isNil() || $this->isMax()) {
return true;
}

return $this->getVariant() === Uuid::RFC_4122;
}
}
