<?php

declare(strict_types=1);

namespace Pest\Arch;

use Closure;
use Pest\Arch\Contracts\ArchExpectation;
use Pest\Expectation;

/**
@mixin


*/
final class GroupArchExpectation implements Contracts\ArchExpectation
{






private function __construct(private readonly Expectation $original, private readonly array $expectations)
{

}







public function ignoring(array|string $targetsOrDependencies): self
{
foreach ($this->expectations as $expectation) {
$expectation->ignoring($targetsOrDependencies);
}

return $this;
}






public function ignoringGlobalFunctions(): self
{
foreach ($this->expectations as $expectation) {
$expectation->ignoringGlobalFunctions();
}

return $this;
}




public function opposite(Closure $callback): self
{
foreach ($this->expectations as $expectation) {
$expectation->opposite($callback);
}

return $this;
}







public static function fromExpectations(Expectation $original, array $expectations): self
{
return new self($original, $expectations);
}







public function __call(string $name, array $arguments): mixed
{
$this->ensureLazyExpectationIsVerified();

return $this->original->$name(...$arguments); 
}






public function __get(string $name): mixed
{
$this->ensureLazyExpectationIsVerified();

return $this->original->$name; 
}




public function mergeExcludeCallbacks(array $excludeCallbacks): void
{
foreach ($this->expectations as $expectation) {
$expectation->mergeExcludeCallbacks($excludeCallbacks);
}
}




public function excludeCallbacks(): array
{
return array_merge(...array_map(
fn (ArchExpectation $expectation): array => $expectation->excludeCallbacks(), $this->expectations,
));
}




public function __destruct()
{
$this->ensureLazyExpectationIsVerified();
}




private function ensureLazyExpectationIsVerified(): void
{
foreach ($this->expectations as $expectation) {
$expectation->ensureLazyExpectationIsVerified();
}
}
}
