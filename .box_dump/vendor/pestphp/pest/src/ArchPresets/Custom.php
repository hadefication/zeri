<?php

declare(strict_types=1);

namespace Pest\ArchPresets;

use Closure;
use Pest\Arch\Contracts\ArchExpectation;
use Pest\Expectation;




final class Custom extends AbstractPreset
{






public function __construct(
private readonly array $userNamespaces,
private readonly string $name,
private readonly Closure $execute,
) {
parent::__construct($userNamespaces);
}




public function name(): string
{
return $this->name;
}




public function execute(): void
{
$this->expectations = ($this->execute)($this->userNamespaces);
}
}
