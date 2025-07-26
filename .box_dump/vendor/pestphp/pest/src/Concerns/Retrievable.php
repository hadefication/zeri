<?php

declare(strict_types=1);

namespace Pest\Concerns;




trait Retrievable
{
/**
@template
@template






*/
private function retrieve(string $key, mixed $value, mixed $default = null): mixed
{
if (is_array($value)) {
return $value[$key] ?? $default;
}


return $value->$key ?? $default;
}
}
