<?php

namespace Illuminate\Support;

use Illuminate\Support\Defer\DeferredCallback;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Symfony\Component\Process\PhpExecutableFinder;

if (! function_exists('Illuminate\Support\defer')) {








function defer(?callable $callback = null, ?string $name = null, bool $always = false)
{
if ($callback === null) {
return app(DeferredCallbackCollection::class);
}

return tap(
new DeferredCallback($callback, $name, $always),
fn ($deferred) => app(DeferredCallbackCollection::class)[] = $deferred
);
}
}

if (! function_exists('Illuminate\Support\php_binary')) {





function php_binary()
{
return (new PhpExecutableFinder)->find(false) ?: 'php';
}
}

if (! function_exists('Illuminate\Support\artisan_binary')) {





function artisan_binary()
{
return defined('ARTISAN_BINARY') ? ARTISAN_BINARY : 'artisan';
}
}
