<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Builder;

use Ramsey\Collection\AbstractCollection;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Nonstandard\UuidBuilder as NonstandardUuidBuilder;
use Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;
use Traversable;

/**
@extends







*/
class BuilderCollection extends AbstractCollection
{
public function getType(): string
{
return UuidBuilderInterface::class;
}

public function getIterator(): Traversable
{
return parent::getIterator();
}






public function unserialize($serialized): void
{

$data = unserialize($serialized, [
'allowed_classes' => [
BrickMathCalculator::class,
GenericNumberConverter::class,
GenericTimeConverter::class,
GuidBuilder::class,
NonstandardUuidBuilder::class,
PhpTimeConverter::class,
Rfc4122UuidBuilder::class,
],
]);

$this->data = array_filter(
$data,
function ($unserialized): bool {
/**
@phpstan-ignore */
return $unserialized instanceof UuidBuilderInterface;
},
);
}
}
