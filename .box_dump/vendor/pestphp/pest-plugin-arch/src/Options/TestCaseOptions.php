<?php

declare(strict_types=1);

namespace Pest\Arch\Options;

use Pest\Arch\Support\UserDefinedFunctions;




final class TestCaseOptions
{





public array $ignore = [];







public function ignore(array|string $targetsOrDependencies): self
{
$targetsOrDependencies = is_array($targetsOrDependencies) ? $targetsOrDependencies : [$targetsOrDependencies];

$this->ignore = [...$this->ignore, ...$targetsOrDependencies];

return $this;
}






public function ignoreGlobalFunctions(): self
{
return $this->ignore(UserDefinedFunctions::get());
}
}
