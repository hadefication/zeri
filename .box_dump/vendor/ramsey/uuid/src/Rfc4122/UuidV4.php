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
final class UuidV4 extends Uuid implements UuidInterface
{









public function __construct(
Rfc4122FieldsInterface $fields,
NumberConverterInterface $numberConverter,
CodecInterface $codec,
TimeConverterInterface $timeConverter,
) {
if ($fields->getVersion() !== Uuid::UUID_TYPE_RANDOM) {
throw new InvalidArgumentException(
'Fields used to create a UuidV4 must represent a version 4 (random) UUID',
);
}

parent::__construct($fields, $numberConverter, $codec, $timeConverter);
}
}
