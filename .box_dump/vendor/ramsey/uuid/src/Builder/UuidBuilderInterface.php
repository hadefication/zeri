<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Builder;

use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\UuidInterface;

/**
@immutable


*/
interface UuidBuilderInterface
{
/**
@pure







*/
public function build(CodecInterface $codec, string $bytes): UuidInterface;
}
