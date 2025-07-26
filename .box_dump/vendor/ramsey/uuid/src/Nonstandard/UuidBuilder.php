<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Nonstandard;

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\UuidInterface;
use Throwable;

/**
@immutable


*/
class UuidBuilder implements UuidBuilderInterface
{





public function __construct(
private NumberConverterInterface $numberConverter,
private TimeConverterInterface $timeConverter,
) {
}

/**
@pure







*/
public function build(CodecInterface $codec, string $bytes): UuidInterface
{
try {
/**
@phpstan-ignore */
return new Uuid($this->buildFields($bytes), $this->numberConverter, $codec, $this->timeConverter);
} catch (Throwable $e) {
/**
@phpstan-ignore */
throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
}
}

/**
@pure


*/
protected function buildFields(string $bytes): Fields
{
/**
@phpstan-ignore */
return new Fields($bytes);
}
}
