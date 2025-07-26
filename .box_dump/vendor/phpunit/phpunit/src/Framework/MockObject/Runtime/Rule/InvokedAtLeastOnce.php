<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Rule;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
@no-named-arguments


*/
final class InvokedAtLeastOnce extends InvocationOrder
{
public function toString(): string
{
return 'invoked at least once';
}







public function verify(): void
{
$count = $this->numberOfInvocations();

if ($count < 1) {
throw new ExpectationFailedException(
'Expected invocation at least once but it never occurred.',
);
}
}

public function matches(BaseInvocation $invocation): bool
{
return true;
}
}
