<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\UnixTimeConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Exception\UnsupportedOperationException;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Rfc4122\UuidInterface as Rfc4122UuidInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Throwable;

/**
@immutable


*/
class UuidBuilder implements UuidBuilderInterface
{
private TimeConverterInterface $unixTimeConverter;










public function __construct(
private NumberConverterInterface $numberConverter,
private TimeConverterInterface $timeConverter,
?TimeConverterInterface $unixTimeConverter = null,
) {
$this->unixTimeConverter = $unixTimeConverter ?? new UnixTimeConverter(new BrickMathCalculator());
}

/**
@pure







*/
public function build(CodecInterface $codec, string $bytes): UuidInterface
{
try {

$fields = $this->buildFields($bytes);

if ($fields->isNil()) {
/**
@phpstan-ignore */
return new NilUuid($fields, $this->numberConverter, $codec, $this->timeConverter);
}

if ($fields->isMax()) {
/**
@phpstan-ignore */
return new MaxUuid($fields, $this->numberConverter, $codec, $this->timeConverter);
}

return match ($fields->getVersion()) {
/**
@phpstan-ignore */
Uuid::UUID_TYPE_TIME => new UuidV1($fields, $this->numberConverter, $codec, $this->timeConverter),
Uuid::UUID_TYPE_DCE_SECURITY
/**
@phpstan-ignore */
=> new UuidV2($fields, $this->numberConverter, $codec, $this->timeConverter),
/**
@phpstan-ignore */
Uuid::UUID_TYPE_HASH_MD5 => new UuidV3($fields, $this->numberConverter, $codec, $this->timeConverter),
/**
@phpstan-ignore */
Uuid::UUID_TYPE_RANDOM => new UuidV4($fields, $this->numberConverter, $codec, $this->timeConverter),
/**
@phpstan-ignore */
Uuid::UUID_TYPE_HASH_SHA1 => new UuidV5($fields, $this->numberConverter, $codec, $this->timeConverter),
Uuid::UUID_TYPE_REORDERED_TIME
/**
@phpstan-ignore */
=> new UuidV6($fields, $this->numberConverter, $codec, $this->timeConverter),
Uuid::UUID_TYPE_UNIX_TIME
/**
@phpstan-ignore */
=> new UuidV7($fields, $this->numberConverter, $codec, $this->unixTimeConverter),
/**
@phpstan-ignore */
Uuid::UUID_TYPE_CUSTOM => new UuidV8($fields, $this->numberConverter, $codec, $this->timeConverter),
default => throw new UnsupportedOperationException(
'The UUID version in the given fields is not supported by this UUID builder',
),
};
} catch (Throwable $e) {
/**
@phpstan-ignore */
throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
}
}

/**
@pure


*/
protected function buildFields(string $bytes): FieldsInterface
{
/**
@phpstan-ignore */
return new Fields($bytes);
}
}
