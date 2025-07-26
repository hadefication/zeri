<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Rule;

use function sprintf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
@no-named-arguments


*/
final class InvokedAtMostCount extends InvocationOrder
{
private readonly int $allowedInvocations;

public function __construct(int $allowedInvocations)
{
$this->allowedInvocations = $allowedInvocations;
}

public function toString(): string
{
return sprintf(
'invoked at most %d time%s',
$this->allowedInvocations,
$this->allowedInvocations !== 1 ? 's' : '',
);
}







public function verify(): void
{
$actualInvocations = $this->numberOfInvocations();

if ($actualInvocations > $this->allowedInvocations) {
throw new ExpectationFailedException(
sprintf(
'Expected invocation at most %d time%s but it occurred %d time%s.',
$this->allowedInvocations,
$this->allowedInvocations !== 1 ? 's' : '',
$actualInvocations,
$actualInvocations !== 1 ? 's' : '',
),
);
}
}

public function matches(BaseInvocation $invocation): bool
{
return true;
}
}
