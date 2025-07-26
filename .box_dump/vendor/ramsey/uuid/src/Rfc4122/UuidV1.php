<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Rfc4122;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Rfc4122\FieldsInterface as Rfc4122FieldsInterface;
use Ramsey\Uuid\Uuid;

/**
@immutable




*/
final class UuidV1 extends Uuid implements UuidInterface
{
use TimeTrait;










public function __construct(
Rfc4122FieldsInterface $fields,
NumberConverterInterface $numberConverter,
CodecInterface $codec,
TimeConverterInterface $timeConverter,
) {
if ($fields->getVersion() !== Uuid::UUID_TYPE_TIME) {
throw new InvalidArgumentException(
'Fields used to create a UuidV1 must represent a version 1 (time-based) UUID',
);
}

parent::__construct($fields, $numberConverter, $codec, $timeConverter);
}
}
