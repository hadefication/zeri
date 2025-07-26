<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Builder;

use PHPUnit\Framework\Constraint\Constraint;

/**
@no-named-arguments


*/
interface MethodNameMatch extends ParametersMatch
{




public function method(Constraint|string $constraint): self;
}
