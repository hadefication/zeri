<?php









declare(strict_types=1);

namespace phpDocumentor\Reflection\Types;

use phpDocumentor\Reflection\Type;

/**
@psalm-immutable






*/
final class Intersection extends AggregatedType
{





public function __construct(array $types)
{
parent::__construct($types, '&');
}
}
