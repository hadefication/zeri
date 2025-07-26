<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Rule;

use function sprintf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
@no-named-arguments


*/
final class InvokedCount extends InvocationOrder
{
private readonly int $expectedCount;

public function __construct(int $expectedCount)
{
$this->expectedCount = $expectedCount;
}

public function isNever(): bool
{
return $this->expectedCount === 0;
}

public function toString(): string
{
return sprintf(
'invoked %d time%s',
$this->expectedCount,
$this->expectedCount !== 1 ? 's' : '',
);
}

public function matches(BaseInvocation $invocation): bool
{
return true;
}







public function verify(): void
{
$actualCount = $this->numberOfInvocations();

if ($actualCount !== $this->expectedCount) {
throw new ExpectationFailedException(
sprintf(
'Method was expected to be called %d time%s, actually called %d time%s.',
$this->expectedCount,
$this->expectedCount !== 1 ? 's' : '',
$actualCount,
$actualCount !== 1 ? 's' : '',
),
);
}
}




protected function invokedDo(BaseInvocation $invocation): void
{
$count = $this->numberOfInvocations();

if ($count > $this->expectedCount) {
$message = $invocation->toString() . ' ';

$message .= match ($this->expectedCount) {
0 => 'was not expected to be called.',
1 => 'was not expected to be called more than once.',
default => sprintf(
'was not expected to be called more than %d times.',
$this->expectedCount,
),
};

throw new ExpectationFailedException($message);
}
}
}
