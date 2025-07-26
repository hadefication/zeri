<?php

declare(strict_types=1);

use Pest\Arch\Concerns\Architectable;
use Pest\PendingCalls\TestCall;
use Pest\Plugin;
use Pest\Support\HigherOrderTapProxy;

Plugin::uses(Architectable::class);

if (! function_exists('arch')) {





function arch(?string $description = null, ?Closure $closure = null): TestCall
{
$test = test(...func_get_args()); 

assert(! $test instanceof HigherOrderTapProxy);

return $test->group('arch');
}
}
