<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Rule;

use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
@no-named-arguments


*/
final class AnyParameters implements ParametersRule
{
public function apply(BaseInvocation $invocation): void
{
}

public function verify(): void
{
}
}
