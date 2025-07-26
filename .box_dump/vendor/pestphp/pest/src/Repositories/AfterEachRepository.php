<?php

declare(strict_types=1);

namespace Pest\Repositories;

use Closure;
use Mockery;
use Pest\PendingCalls\AfterEachCall;
use Pest\Support\ChainableClosure;
use Pest\Support\NullClosure;




final class AfterEachRepository
{



private array $state = [];




public function set(string $filename, AfterEachCall $afterEachCall, Closure $afterEachTestCase): void
{
if (array_key_exists($filename, $this->state)) {
$fromAfterEachTestCase = $this->state[$filename];

$afterEachTestCase = ChainableClosure::bound($fromAfterEachTestCase, $afterEachTestCase)
->bindTo($afterEachCall, $afterEachCall::class);
}

assert($afterEachTestCase instanceof Closure);

$this->state[$filename] = $afterEachTestCase;
}




public function get(string $filename): Closure
{
$afterEach = $this->state[$filename] ?? NullClosure::create();

return ChainableClosure::bound(function (): void {
if (class_exists(Mockery::class)) {
if ($container = Mockery::getContainer()) {

$this->addToAssertionCount($container->mockery_getExpectationCount());
}

Mockery::close();
}
}, $afterEach);
}
}
