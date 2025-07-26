<?php

declare(strict_types=1);

namespace Pest\Support;

use Closure as BaseClosure;
use Pest\Exceptions\ShouldNotHappen;




final class Closure
{





public static function bind(?BaseClosure $closure, ?object $newThis, object|string|null $newScope = 'static'): BaseClosure
{
if (! $closure instanceof \Closure) {
throw ShouldNotHappen::fromMessage('Could not bind null closure.');
}


$closure = BaseClosure::bind($closure, $newThis, $newScope);

if (! $closure instanceof \Closure) {
throw ShouldNotHappen::fromMessage('Could not bind closure.');
}

return $closure;
}
}
