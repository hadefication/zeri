<?php

declare(strict_types=1);

namespace Pest\PendingCalls;

use Closure;
use Pest\Support\Backtrace;
use Pest\TestSuite;




final class DescribeCall
{





private static array $describing = [];




private ?BeforeEachCall $currentBeforeEachCall = null;




public function __construct(
public readonly TestSuite $testSuite,
public readonly string $filename,
public readonly string $description,
public readonly Closure $tests
) {

}






public static function describing(): array
{
return self::$describing;
}




public function __destruct()
{
unset($this->currentBeforeEachCall);

self::$describing[] = $this->description;

try {
($this->tests)();
} finally {
array_pop(self::$describing);
}
}






public function __call(string $name, array $arguments): self
{
$filename = Backtrace::file();

if (! $this->currentBeforeEachCall instanceof \Pest\PendingCalls\BeforeEachCall) {
$this->currentBeforeEachCall = new BeforeEachCall(TestSuite::getInstance(), $filename);

$this->currentBeforeEachCall->describing[] = $this->description;
}

$this->currentBeforeEachCall->{$name}(...$arguments);

return $this;
}
}
