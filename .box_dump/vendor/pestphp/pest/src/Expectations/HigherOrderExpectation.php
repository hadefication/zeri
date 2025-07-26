<?php

declare(strict_types=1);

namespace Pest\Expectations;

use Closure;
use Pest\Concerns\Retrievable;
use Pest\Expectation;

/**
@template
@template
@mixin



*/
final class HigherOrderExpectation
{
use Retrievable;




private Expectation|EachExpectation $expectation;




private bool $opposite = false;




private bool $shouldReset = false;







public function __construct(private readonly Expectation $original, mixed $value)
{
$this->expectation = $this->expect($value);
}






public function not(): self
{
$this->opposite = ! $this->opposite;

return $this;
}

/**
@template





*/
public function expect(mixed $value): Expectation
{
return new Expectation($value);
}

/**
@template





*/
public function and(mixed $value): Expectation
{
return $this->expect($value);
}








public function scoped(Closure $expectation): self
{
$expectation->__invoke($this->expectation);

return new self($this->original, $this->original->value);
}






public function json(): self
{
return new self($this->original, $this->expectation->json()->value);
}







public function __call(string $name, array $arguments): self
{
if (! $this->expectationHasMethod($name)) {

return new self($this->original, $this->getValue()->$name(...$arguments));
}

return $this->performAssertion($name, $arguments);
}






public function __get(string $name): self
{
if ($name === 'not') {
return $this->not();
}

if (! $this->expectationHasMethod($name)) {

$value = $this->getValue();

return new self($this->original, $this->retrieve($name, $value));
}

return $this->performAssertion($name, []);
}




private function expectationHasMethod(string $name): bool
{
if (method_exists($this->original, $name)) {
return true;
}
if ($this->original::hasMethod($name)) {
return true;
}

return $this->original::hasExtend($name);
}






private function getValue(): mixed
{
return $this->shouldReset ? $this->original->value : $this->expectation->value;
}







private function performAssertion(string $name, array $arguments): self
{

$this->expectation = ($this->opposite ? $this->expectation->not() : $this->expectation)->{$name}(...$arguments);

$this->opposite = false;
$this->shouldReset = true;

return $this;
}
}
