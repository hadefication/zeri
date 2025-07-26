<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\Time\DegradedTimeConverter;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\DegradedUuid;
use Ramsey\Uuid\Rfc4122\Fields as Rfc4122Fields;
use Ramsey\Uuid\UuidInterface;

/**
@immutable


*/
class DegradedUuidBuilder implements UuidBuilderInterface
{
private TimeConverterInterface $timeConverter;






public function __construct(
private NumberConverterInterface $numberConverter,
?TimeConverterInterface $timeConverter = null
) {
$this->timeConverter = $timeConverter ?: new DegradedTimeConverter();
}

/**
@phpstan-impure







*/
public function build(CodecInterface $codec, string $bytes): UuidInterface
{
return new DegradedUuid(new Rfc4122Fields($bytes), $this->numberConverter, $codec, $this->timeConverter);
}
}
