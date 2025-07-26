<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Lazy;

use DateTimeInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Rfc4122\UuidV6;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use ValueError;

use function assert;
use function bin2hex;
use function hex2bin;
use function sprintf;
use function str_replace;
use function substr;
















final class LazyUuidFromString implements UuidInterface
{
public const VALID_REGEX = '/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/ms';

private ?UuidInterface $unwrapped = null;




public function __construct(private string $uuid)
{
}

public static function fromBytes(string $bytes): self
{
$base16Uuid = bin2hex($bytes);

return new self(
substr($base16Uuid, 0, 8)
. '-'
. substr($base16Uuid, 8, 4)
. '-'
. substr($base16Uuid, 12, 4)
. '-'
. substr($base16Uuid, 16, 4)
. '-'
. substr($base16Uuid, 20, 12)
);
}

public function serialize(): string
{
return $this->uuid;
}




public function __serialize(): array
{
return ['string' => $this->uuid];
}






public function unserialize(string $data): void
{
$this->uuid = $data;
}




public function __unserialize(array $data): void
{

if (!isset($data['string'])) {
throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
}


$this->unserialize($data['string']);
}

public function getNumberConverter(): NumberConverterInterface
{
return ($this->unwrapped ?? $this->unwrap())->getNumberConverter();
}




public function getFieldsHex(): array
{
return ($this->unwrapped ?? $this->unwrap())->getFieldsHex();
}

public function getClockSeqHiAndReservedHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getClockSeqHiAndReservedHex();
}

public function getClockSeqLowHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getClockSeqLowHex();
}

public function getClockSequenceHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getClockSequenceHex();
}

public function getDateTime(): DateTimeInterface
{
return ($this->unwrapped ?? $this->unwrap())->getDateTime();
}

public function getLeastSignificantBitsHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getLeastSignificantBitsHex();
}

public function getMostSignificantBitsHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getMostSignificantBitsHex();
}

public function getNodeHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getNodeHex();
}

public function getTimeHiAndVersionHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getTimeHiAndVersionHex();
}

public function getTimeLowHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getTimeLowHex();
}

public function getTimeMidHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getTimeMidHex();
}

public function getTimestampHex(): string
{
return ($this->unwrapped ?? $this->unwrap())->getTimestampHex();
}

public function getUrn(): string
{
return ($this->unwrapped ?? $this->unwrap())->getUrn();
}

public function getVariant(): ?int
{
return ($this->unwrapped ?? $this->unwrap())->getVariant();
}

public function getVersion(): ?int
{
return ($this->unwrapped ?? $this->unwrap())->getVersion();
}

public function compareTo(UuidInterface $other): int
{
return ($this->unwrapped ?? $this->unwrap())->compareTo($other);
}

public function equals(?object $other): bool
{
if (!$other instanceof UuidInterface) {
return false;
}

return $this->uuid === $other->toString();
}

public function getBytes(): string
{
/**
@phpstan-ignore

*/
return (string) hex2bin(str_replace('-', '', $this->uuid));
}

public function getFields(): FieldsInterface
{
return ($this->unwrapped ?? $this->unwrap())->getFields();
}

public function getHex(): Hexadecimal
{
return ($this->unwrapped ?? $this->unwrap())->getHex();
}

public function getInteger(): IntegerObject
{
return ($this->unwrapped ?? $this->unwrap())->getInteger();
}

public function toString(): string
{
return $this->uuid;
}

public function __toString(): string
{
return $this->uuid;
}

public function jsonSerialize(): string
{
return $this->uuid;
}






public function getClockSeqHiAndReserved(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

$fields = $instance->getFields();
assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

return $instance->getNumberConverter()->fromHex($fields->getClockSeqHiAndReserved()->toString());
}






public function getClockSeqLow(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

$fields = $instance->getFields();
assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

return $instance->getNumberConverter()->fromHex($fields->getClockSeqLow()->toString());
}






public function getClockSequence(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

$fields = $instance->getFields();
assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

return $instance->getNumberConverter()->fromHex($fields->getClockSeq()->toString());
}





public function getLeastSignificantBits(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

return $instance->getNumberConverter()->fromHex(substr($instance->getHex()->toString(), 16));
}





public function getMostSignificantBits(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

return $instance->getNumberConverter()->fromHex(substr($instance->getHex()->toString(), 0, 16));
}






public function getNode(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

$fields = $instance->getFields();
assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

return $instance->getNumberConverter()->fromHex($fields->getNode()->toString());
}






public function getTimeHiAndVersion(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

$fields = $instance->getFields();
assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

return $instance->getNumberConverter()->fromHex($fields->getTimeHiAndVersion()->toString());
}






public function getTimeLow(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

$fields = $instance->getFields();
assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

return $instance->getNumberConverter()->fromHex($fields->getTimeLow()->toString());
}






public function getTimeMid(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

$fields = $instance->getFields();
assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

return $instance->getNumberConverter()->fromHex($fields->getTimeMid()->toString());
}






public function getTimestamp(): string
{
$instance = ($this->unwrapped ?? $this->unwrap());

$fields = $instance->getFields();
assert($fields instanceof \Ramsey\Uuid\Rfc4122\FieldsInterface);

if ($fields->getVersion() !== 1) {
throw new UnsupportedOperationException('Not a time-based UUID');
}

return $instance->getNumberConverter()->fromHex($fields->getTimestamp()->toString());
}

public function toUuidV1(): UuidV1
{
$instance = ($this->unwrapped ?? $this->unwrap());

if ($instance instanceof UuidV1) {
return $instance;
}

assert($instance instanceof UuidV6);

return $instance->toUuidV1();
}

public function toUuidV6(): UuidV6
{
$instance = ($this->unwrapped ?? $this->unwrap());

assert($instance instanceof UuidV6);

return $instance;
}

private function unwrap(): UuidInterface
{
return $this->unwrapped = (new UuidFactory())->fromString($this->uuid);
}
}
