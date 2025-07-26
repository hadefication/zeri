<?php

declare(strict_types=1);

namespace Pest\Support;

use Closure;




final class NullClosure
{



public static function create(): Closure
{
return Closure::fromCallable(function (): void {});
}
}
