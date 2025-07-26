<?php

declare(strict_types=1);

namespace Pest\Arch\Contracts;

use Pest\Expectation;
use PHPUnit\Architecture\Elements\ObjectDescription;

/**
@mixin


*/
interface ArchExpectation
{






public function ignoring(array|string $targetsOrDependencies): self;






public function ignoringGlobalFunctions(): self;








public function mergeExcludeCallbacks(array $callbacks): void;








public function excludeCallbacks(): array;
}
