<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Rule;

use function count;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
use PHPUnit\Framework\SelfDescribing;

/**
@no-named-arguments


*/
abstract class InvocationOrder implements SelfDescribing
{



private array $invocations = [];

public function numberOfInvocations(): int
{
return count($this->invocations);
}

public function hasBeenInvoked(): bool
{
return count($this->invocations) > 0;
}

final public function invoked(BaseInvocation $invocation): void
{
$this->invocations[] = $invocation;

$this->invokedDo($invocation);
}

abstract public function matches(BaseInvocation $invocation): bool;

abstract public function verify(): void;

protected function invokedDo(BaseInvocation $invocation): void
{
}
}
