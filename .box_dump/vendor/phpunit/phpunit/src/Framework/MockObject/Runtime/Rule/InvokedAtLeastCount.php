<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Rule;

use function sprintf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
@no-named-arguments


*/
final class InvokedAtLeastCount extends InvocationOrder
{
private readonly int $requiredInvocations;

public function __construct(int $requiredInvocations)
{
$this->requiredInvocations = $requiredInvocations;
}

public function toString(): string
{
return sprintf(
'invoked at least %d time%s',
$this->requiredInvocations,
$this->requiredInvocations !== 1 ? 's' : '',
);
}







public function verify(): void
{
$actualInvocations = $this->numberOfInvocations();

if ($actualInvocations < $this->requiredInvocations) {
throw new ExpectationFailedException(
sprintf(
'Expected invocation at least %d time%s but it occurred %d time%s.',
$this->requiredInvocations,
$this->requiredInvocations !== 1 ? 's' : '',
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
