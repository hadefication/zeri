<?php

declare(strict_types=1);

namespace Pest\Repositories;

use Closure;
use Pest\Exceptions\BeforeAllAlreadyExist;
use Pest\Support\NullClosure;
use Pest\Support\Reflection;




final class BeforeAllRepository
{



private array $state = [];




public function pop(string $filename): Closure
{
$closure = $this->get($filename);

unset($this->state[$filename]);

return $closure;
}




public function set(Closure $closure): void
{
$filename = Reflection::getFileNameFromClosure($closure);

if (array_key_exists($filename, $this->state)) {
throw new BeforeAllAlreadyExist($filename);
}

$this->state[$filename] = $closure;
}




public function get(string $filename): Closure
{
return $this->state[$filename] ?? NullClosure::create();
}
}
