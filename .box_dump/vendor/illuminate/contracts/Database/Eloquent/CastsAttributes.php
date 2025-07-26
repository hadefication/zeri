<?php

namespace Illuminate\Contracts\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
@template
@template
*/
interface CastsAttributes
{









public function get(Model $model, string $key, mixed $value, array $attributes);










public function set(Model $model, string $key, mixed $value, array $attributes);
}
