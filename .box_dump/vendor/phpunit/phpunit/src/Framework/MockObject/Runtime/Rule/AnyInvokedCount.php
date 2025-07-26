<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Rule;

use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
@no-named-arguments


*/
final class AnyInvokedCount extends InvocationOrder
{
public function toString(): string
{
return 'invoked zero or more times';
}

public function verify(): void
{
}

public function matches(BaseInvocation $invocation): bool
{
return true;
}
}
