<?php

namespace Illuminate\Contracts\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

interface SerializesCastableAttributes
{









public function serialize(Model $model, string $key, mixed $value, array $attributes);
}
