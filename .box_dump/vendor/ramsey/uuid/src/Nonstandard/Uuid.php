<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Nonstandard;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Uuid as BaseUuid;

/**
@immutable
@pure


*/
final class Uuid extends BaseUuid
{
public function __construct(
Fields $fields,
NumberConverterInterface $numberConverter,
CodecInterface $codec,
TimeConverterInterface $timeConverter,
) {
parent::__construct($fields, $numberConverter, $codec, $timeConverter);
}
}
