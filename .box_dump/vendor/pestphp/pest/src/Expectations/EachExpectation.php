<?php

declare(strict_types=1);

namespace Pest\Expectations;

use Pest\Expectation;

use function expect;

/**
@template
@mixin



*/
final class EachExpectation
{



private bool $opposite = false;






public function __construct(private readonly Expectation $original) {}

/**
@template





*/
public function and(mixed $value): Expectation
{
return $this->original->and($value);
}






public function not(): self
{
$this->opposite = true;

return $this;
}







public function __call(string $name, array $arguments): self
{
foreach ($this->original->value as $item) {

$this->opposite ? expect($item)->not()->$name(...$arguments) : expect($item)->$name(...$arguments);
}

$this->opposite = false;

return $this;
}






public function __get(string $name): self
{

return $this->$name();
}
}
