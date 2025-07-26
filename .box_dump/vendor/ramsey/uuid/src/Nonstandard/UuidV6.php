<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Nonstandard;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Rfc4122\TimeTrait;
use Ramsey\Uuid\Rfc4122\UuidInterface;
use Ramsey\Uuid\Rfc4122\UuidV1;
use Ramsey\Uuid\Uuid as BaseUuid;

/**
@immutable









*/
class UuidV6 extends BaseUuid implements UuidInterface
{
use TimeTrait;










public function __construct(
Rfc4122FieldsInterface $fields,
NumberConverterInterface $numberConverter,
CodecInterface $codec,
TimeConverterInterface $timeConverter,
) {
if ($fields->getVersion() !== BaseUuid::UUID_TYPE_REORDERED_TIME) {
throw new InvalidArgumentException(
'Fields used to create a UuidV6 must represent a version 6 (reordered time) UUID',
);
}

parent::__construct($fields, $numberConverter, $codec, $timeConverter);
}




public function toUuidV1(): UuidV1
{
$hex = $this->getHex()->toString();
$hex = substr($hex, 7, 5)
. substr($hex, 13, 3)
. substr($hex, 3, 4)
. '1' . substr($hex, 0, 3)
. substr($hex, 16);


$uuid = BaseUuid::fromBytes((string) hex2bin($hex));

return $uuid->toUuidV1();
}




public static function fromUuidV1(UuidV1 $uuidV1): \Ramsey\Uuid\Rfc4122\UuidV6
{
$hex = $uuidV1->getHex()->toString();
$hex = substr($hex, 13, 3)
. substr($hex, 8, 4)
. substr($hex, 0, 5)
. '6' . substr($hex, 5, 3)
. substr($hex, 16);


$uuid = BaseUuid::fromBytes((string) hex2bin($hex));

return $uuid->toUuidV6();
}
}
