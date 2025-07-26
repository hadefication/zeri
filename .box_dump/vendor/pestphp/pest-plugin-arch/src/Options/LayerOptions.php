<?php

declare(strict_types=1);

namespace Pest\Arch\Options;

use Pest\Arch\SingleArchExpectation;
use PHPUnit\Architecture\Elements\ObjectDescription;




final class LayerOptions
{




private function __construct(
public readonly array $exclude,
public readonly array $excludeCallbacks,
) {

}




public static function fromExpectation(SingleArchExpectation $expectation): self
{

$exclude = array_merge(
test()->arch()->ignore, 
$expectation->ignoring,
);

return new self($exclude, $expectation->excludeCallbacks());
}
}
