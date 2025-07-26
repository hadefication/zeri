<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Exception\BuilderNotFoundException;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\UuidInterface;

/**
@immutable


*/
class FallbackBuilder implements UuidBuilderInterface
{



public function __construct(private iterable $builders)
{
}

/**
@pure







*/
public function build(CodecInterface $codec, string $bytes): UuidInterface
{
$lastBuilderException = null;

foreach ($this->builders as $builder) {
try {
return $builder->build($codec, $bytes);
} catch (UnableToBuildUuidException $exception) {
$lastBuilderException = $exception;

continue;
}
}

throw new BuilderNotFoundException(
'Could not find a suitable builder for the provided codec and fields',
0,
$lastBuilderException,
);
}
}
