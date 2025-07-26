<?php declare(strict_types=1);








namespace PHPUnit\Runner;

use function array_map;
use function usort;

/**
@no-named-arguments


*/
final class HookMethodCollection
{
private readonly bool $shouldPrepend;




private array $hookMethods;

public static function defaultBeforeClass(): self
{
return new self(new HookMethod('setUpBeforeClass', 0), true);
}

public static function defaultBefore(): self
{
return new self(new HookMethod('setUp', 0), true);
}

public static function defaultPreCondition(): self
{
return new self(new HookMethod('assertPreConditions', 0), true);
}

public static function defaultPostCondition(): self
{
return new self(new HookMethod('assertPostConditions', 0), false);
}

public static function defaultAfter(): self
{
return new self(new HookMethod('tearDown', 0), false);
}

public static function defaultAfterClass(): self
{
return new self(new HookMethod('tearDownAfterClass', 0), false);
}

private function __construct(HookMethod $default, bool $shouldPrepend)
{
$this->hookMethods = [$default];
$this->shouldPrepend = $shouldPrepend;
}

public function add(HookMethod $hookMethod): self
{
if ($this->shouldPrepend) {
$this->hookMethods = [$hookMethod, ...$this->hookMethods];
} else {
$this->hookMethods[] = $hookMethod;
}

return $this;
}




public function methodNamesSortedByPriority(): array
{
$hookMethods = $this->hookMethods;

usort($hookMethods, static fn (HookMethod $a, HookMethod $b) => $b->priority() <=> $a->priority());

return array_map(
static fn (HookMethod $hookMethod) => $hookMethod->methodName(),
$hookMethods,
);
}
}
