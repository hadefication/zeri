<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Builder;

/**
@no-named-arguments


*/
interface ParametersMatch extends Stub
{



public function after(string $id): Stub;
















public function with(mixed ...$arguments): self;










public function withAnyParameters(): self;
}
