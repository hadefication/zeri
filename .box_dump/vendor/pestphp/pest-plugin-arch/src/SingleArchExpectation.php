<?php

declare(strict_types=1);

namespace Pest\Arch;

use Closure;
use Pest\Arch\Options\LayerOptions;
use Pest\Arch\Support\UserDefinedFunctions;
use Pest\Expectation;
use Pest\TestSuite;
use PHPUnit\Architecture\Elements\ObjectDescription;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
@mixin


*/
final class SingleArchExpectation implements Contracts\ArchExpectation
{



private ?Closure $opposite = null;




private bool $lazyExpectationVerified = false;






public array $ignoring = [];






private array $excludeCallbacks = [];






private function __construct(private readonly Expectation $expectation, private readonly Closure $lazyExpectation)
{

}




public function ignoring(array|string $targetsOrDependencies): self
{
$targetsOrDependencies = is_array($targetsOrDependencies) ? $targetsOrDependencies : [$targetsOrDependencies];

$this->ignoring = array_unique([...$this->ignoring, ...$targetsOrDependencies]);

return $this;
}




public function ignoringGlobalFunctions(): self
{
return $this->ignoring(UserDefinedFunctions::get());
}




public function opposite(Closure $callback): self
{
$this->opposite = $callback;

return $this;
}






public static function fromExpectation(Expectation $expectation, Closure $lazyExpectation): self
{
return new self($expectation, $lazyExpectation);
}







public function __call(string $name, array $arguments): mixed
{
$this->ensureLazyExpectationIsVerified();

return $this->expectation->$name(...$arguments); 
}






public function __get(string $name): mixed
{
$this->ensureLazyExpectationIsVerified();

return $this->expectation->$name; 
}




public function mergeExcludeCallbacks(array $callbacks): void
{
$this->excludeCallbacks = [...$this->excludeCallbacks, ...$callbacks];
}






public function excludeCallbacks(): array
{
return $this->excludeCallbacks;
}




public function __destruct()
{
$this->ensureLazyExpectationIsVerified();
}




public function ensureLazyExpectationIsVerified(): void
{
if (TestSuite::getInstance()->test instanceof TestCase && ! $this->lazyExpectationVerified) {
$this->lazyExpectationVerified = true;

$e = null;

$options = LayerOptions::fromExpectation($this);

try {
($this->lazyExpectation)($options);
} catch (ExpectationFailedException|AssertionFailedError $e) {
if (! $this->opposite instanceof \Closure) {
throw $e;
}
}

if (! $this->opposite instanceof Closure) {
return;
}
if (! is_null($e)) {
return;
}

($this->opposite)(); 
}
}
}
