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
final class UuidV8 extends Uuid implements UuidInterface
{









public function __construct(
Rfc4122FieldsInterface $fields,
NumberConverterInterface $numberConverter,
CodecInterface $codec,
TimeConverterInterface $timeConverter,
) {
if ($fields->getVersion() !== Uuid::UUID_TYPE_CUSTOM) {
throw new InvalidArgumentException(
'Fields used to create a UuidV8 must represent a version 8 (custom format) UUID',
);
}

parent::__construct($fields, $numberConverter, $codec, $timeConverter);
}
}
