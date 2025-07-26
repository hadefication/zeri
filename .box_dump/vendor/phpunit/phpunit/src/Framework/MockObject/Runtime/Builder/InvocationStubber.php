<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Builder;

use PHPUnit\Framework\MockObject\Stub\Stub;
use Throwable;

/**
@no-named-arguments
*/
interface InvocationStubber
{
public function will(Stub $stub): Identity;




public function willReturn(mixed $value, mixed ...$nextValues): self;




public function willReturnReference(mixed &$reference): self;






public function willReturnMap(array $valueMap): self;




public function willReturnArgument(int $argumentIndex): self;




public function willReturnCallback(callable $callback): self;




public function willReturnSelf(): self;




public function willReturnOnConsecutiveCalls(mixed ...$values): self;




public function willThrowException(Throwable $exception): self;
}
